<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Entity\UserAction;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private NotificationRepository $notificationRepository
    ) {}

    /**
     * Créer une notification
     */
    public function create(
        User $user,
        string $title,
        string $message,
        string $type,
        ?UserAction $userAction = null
    ): Notification {
        $notification = new Notification();

        $notification->setUser($user);
        $notification->setTitle($title);
        $notification->setMessage($message);
        $notification->setType($type);
        $notification->setIsRead(false);
        $notification->setReceivedAt(new \DateTimeImmutable());
        $notification->setCreatedAt(new \DateTimeImmutable());
        $notification->setUpdatedAt(new \DateTimeImmutable());

        if ($userAction) {
            $notification->setUserAction($userAction);
        }

        $this->em->persist($notification);
        $this->em->flush();

        return $notification;
    }

    /**
     * Marquer comme lu
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->setIsRead(true);
        $notification->setUpdatedAt(new \DateTimeImmutable());

        $this->em->flush();
    }

    /**
     * Supprimer une notification
     */
    public function delete(Notification $notification): void
    {
        $this->em->remove($notification);
        $this->em->flush();
    }

    /**
     * Récupérer les notifications d'un utilisateur
     */
    public function getUserNotifications(User $user): array
    {
        return $this->notificationRepository->findByUser($user);
    }
}