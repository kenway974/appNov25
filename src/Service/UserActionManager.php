<?php

namespace App\Service;

use App\Entity\UserAction;
use App\Entity\User;
use App\Repository\UserActionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Service\NotificationService;
use DateTime;

class UserActionManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $dispatcher,
        private UserActionRepository $userActionRepository,
        private NotificationService $notificationService
    ) {}

    /**
     * Crée une nouvelle UserAction pour un utilisateur.
     */
    public function create(User $user, UserAction $userAction, array $formData = []): void
    {
        $now = new DateTime();

        // Associe l'utilisateur
        $userAction->setUser($user);
        $userAction->setLastUpdate($now);

        // Définit le statut (par défaut "À faire")
        $status = $formData['status'] ?? 'À faire';
        $userAction->setStatus($status);

        // Définit la deadline (aujourd'hui si non fournie)
        if (!empty($formData['deadline'])) {
            $deadline = new DateTime($formData['deadline']);
        } else {
            $deadline = (clone $now);
        }

        $userAction->setDeadline($deadline);

        // Vérifie si l'action est récurrente
        $isRecurring = $userAction->getAction()->isRecurring();
        $userAction->setIsRecurring($isRecurring);

        if ($isRecurring) {

            // Fréquence (par défaut 1 jour)
            $frequency = isset($formData['frequency']) 
                ? (int) $formData['frequency'] 
                : 1;

            $userAction->setFrequency($frequency);

            // Pour une action récurrente :
            // startDate = deadline initiale
            $userAction->setStartDate(clone $deadline);

        } else {
            // Action ponctuelle → pas de fréquence
            $userAction->setFrequency(null);
        }

        $this->em->persist($userAction);
        $this->em->flush();
    }

    /**
     * Marque une UserAction comme complétée.
     */
    public function completeUserAction(UserAction $userAction): void
    {
        $now = new DateTime();

        // Augmente le score du besoin associé
        $userNeed = $userAction->getUserNeed();
        if ($userNeed) {
            $userNeed->setScore(100);
        }

        $notification = $userAction->getNotification();
        if ($notification) {
            $this->notificationService->delete($notification);
        }

        if ($userAction->isRecurring()==1) {

            $userAction->setCompletions($userAction->getCompletions() + 1);
            $userAction->setLastUpdate($now);
            $userAction->setStatus("Déjà essayée");

            $frequency = $userAction->getFrequency();

            if ($frequency) {
                // ⚠️ clone pour éviter bug DateTime mutable
                $deadline = (clone $now)->modify("+{$frequency} days");
                $userAction->setDeadline($deadline);
            }

            $this->em->flush();

        } else {
            // Action ponctuelle → suppression
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
}