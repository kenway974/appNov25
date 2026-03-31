<?php

namespace App\Service;

use App\Document\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

class NotificationService
{
    public function __construct(private DocumentManager $dm, private NotificationRepository $notificationRepository) {}

    /**
     * Crée une notification
     */
    public function createNotification(int $userId, int $userActionId, string $title, string $message, string $type, array $context = []
    ): Notification {
        $notification = new Notification();

        $notification->setUserId($userId)
            ->setUserActionId($userActionId)
            ->setTitle($title)
            ->setMessage($message)
            ->setType($type)
            ->setIsRead(false)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setContext($context);

        $this->dm->persist($notification);
        $this->dm->flush();

        return $notification;
    }

    /**
     * Crée une notification liée à une deadline d'action.
     * Évite les doublons pour une même action le même jour.
     */
    public function createDeadlineNotification(int $userId, int $userActionId, string $actionTitle
    ): ?Notification {

        $today = (new \DateTimeImmutable())->format('Y-m-d');

        // Vérifie doublon
        $existing = $this->notificationRepository
            ->findLatestDeadlineForUserAction($userId, $userActionId);


        if ($existing && $existing->getCreatedAt()->format('Y-m-d') === $today) {
            return null; // Déjà notifié aujourd'hui
        }

        return $this->createNotification(
            $userId,
            $userActionId,
            'Action à faire aujourd’hui',
            sprintf('Votre action "%s" arrive à échéance.', $actionTitle),
            'deadline',
            [
                'entity' => 'user_action',
                'user_action_id' => $userActionId,
                'title' => $actionTitle,
            ]
        );
    }

    /**
     * Récupère les notifications d'un utilisateur.
     */
    public function getUserNotifications(
    int $userId,
    bool $onlyUnread = false,
    int $limit = 10
    ): iterable {
        if ($onlyUnread) {
            return $this->notificationRepository->findByIsRead($userId, false, $limit);
        }

        return $this->notificationRepository->findByUser($userId, $limit);
    }

    /**
     * Marque une notification comme lue.
     */
    public function markAsRead(string $notificationId): void
    {
        $notification = $this->dm
            ->getRepository(Notification::class)
            ->find($notificationId);

        if (!$notification) {
            return;
        }

        $notification->setIsRead(true);
        $this->dm->flush();
    }

    /**
     * Supprime une notification.
     */
    public function delete(string $notificationId): void
    {
        $notification = $this->dm
            ->getRepository(Notification::class)
            ->find($notificationId);

        if (!$notification) {
            return;
        }

        $this->dm->remove($notification);
        $this->dm->flush();
    }
}
