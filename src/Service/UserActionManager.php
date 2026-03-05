<?php

namespace App\Service;

use App\Entity\UserAction;
use App\Repository\UserActionRepository;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\NotificationService;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\Action\{UserActionAddedEvent, UserActionUpdatedEvent, UserActionCompletedEvent};

class UserActionManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $dispatcher,
        private UserActionRepository $userActionRepository,
        private NotificationService $notificationService,
    ) {}

    /**
     * Crée une nouvelle UserAction pour un utilisateur.
     */
    public function create(User $user, UserAction $userAction, array $formData = []): void
    {
        $now = new DateTime();

        $userAction->setUser($user);
        $userAction->setLastUpdate($now);

        // Status (toujours défini)
        $status = $formData['status'] ?? 'À faire';
        $userAction->setStatus($status);

        // Deadline (toujours défini)
        if (!empty($formData['deadline'])) {
            $deadline = new DateTime($formData['deadline']);
        } else {
            $deadline = (clone $now);
        }

        $userAction->setDeadline($deadline);

        // IsRecurring (toujours défini)
        $isRecurring = $userAction->getAction()->isRecurring();
        $userAction->setIsRecurring($isRecurring);

        if ($isRecurring) {

            // Frequency obligatoire si récurrent
            $frequency = isset($formData['frequency']) 
                ? (int) $formData['frequency'] 
                : 1;

            $userAction->setFrequency($frequency);

            // Pour une action récurrente :
            // startDate = deadline
            $userAction->setStartDate(clone $deadline);

        } else {

            // Action ponctuelle
            $userAction->setFrequency(null);
        }

        $this->em->persist($userAction);
        $this->em->flush();
    }


    /**
     * Met à jour une UserAction existante.
     */
    public function completeUserAction(UserAction $userAction): void
    {
        // augemnte le socre du userneed associé
        $userNeed = $userAction->getUserNeed();
        if ($userNeed) {
            $userNeed->setScore(100);
        }

        $now = new DateTime();
        // Si l'action est récurrente, on met à jour les champs pour la prochaine occurrence
        if ($userAction->isRecurring()) {
            $userAction->setCompletions($userAction->getCompletions() + 1);
            $userAction->setLastUpdate($now);
            $userAction->setStatus("Déjà essayée");     
            
            $frequency = $userAction->getFrequency();
            if ($frequency) { // update deadline
                $deadline = $now->modify("+{$frequency} days");
                $userAction->setDeadline($deadline);
            }

            $this->em->flush();
        } else {
            // Action ponctuelle : supprimer le userAction
            $this->em->remove($userAction);
            $this->em->flush();
        }
    }

    /**
     * Supprime une UserAction.
     */
    public function delete(UserAction $userAction): void
    {
        $this->em->remove($userAction);
        $this->em->flush();
    }

    /**
     * Vérifie les deadlines et envoie une notification si nécessaire.
     */
    public function checkDeadlinesAndNotify(): void
    {
        $now = new DateTime();

        // Récupère toutes les UserAction
        $userActions = $this->userActionRepository->findAll();

        foreach ($userActions as $userAction) {

            $deadline = $userAction->getDeadline();

            if (!$deadline) {
                continue;
            }

            // On compare uniquement la date (pas l'heure exacte)
            if ($deadline->format('Y-m-d') === $now->format('Y-m-d')) {

                $user = $userAction->getUser();

                $this->notificationService->createNotification(
                    $user->getId(),
                    'Action à faire aujourd’hui',
                    'Votre action "' . $userAction->getAction()->getTitle() . '" arrive à échéance.',
                    'deadline',
                    [
                        'entity' => 'user_action',
                        'user_action_id' => $userAction->getId(),
                        'title' => $userAction->getAction()->getTitle(),
                    ]
                );
            }
        }
    }
}
