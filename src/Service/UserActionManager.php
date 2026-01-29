<?php

namespace App\Service;

use App\Entity\UserAction;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\Action\{UserActionAddedEvent, UserActionUpdatedEvent, UserActionCompletedEvent};

class UserActionManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $dispatcher // <- ajout
    ) {}

    /**
     * Crée une nouvelle UserAction pour un utilisateur.
     */
    public function create(User $user, UserAction $userAction): void
    {
        $now = new DateTime();
        $userAction->setUser($user);
        $userAction->setStartDate($now);
        $userAction->setDeadline($userAction->getStartDate()->modify('+ 1 day'));
        $userAction->setLastUpdate($now);
        $userAction->setFrequency(7);
        $userAction->setIsRecurring(false);
        $userAction->setStatus("À faire");

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

        if ($userAction->isRecurring()) {
            $userAction->setCompletions($userAction->getCompletions() + 1);
            $userAction->setLastUpdate($now);
            $userAction->setStatus("Déjà essayée");

            $frequency = $userAction->getFrequency();
            if ($frequency) {
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
}
