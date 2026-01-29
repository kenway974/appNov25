<?php

namespace App\Service;

use App\Document\Notification;
use Doctrine\ODM\MongoDB\DocumentManager;

class NotificationService
{
    public function __construct(private DocumentManager $dm) {}

    public function createNotification(int $userId, string $title, string $message, string $type, array $context = []): Notification
    {
        $notification = new Notification();
        $notification->setUserId($userId)
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

    public function getUserNotifications(int $userId, bool $onlyUnread = false): array
    {
        $criteria = ['userId' => $userId];
        if ($onlyUnread) {
            $criteria['isRead'] = false;
        }

        return $this->dm->getRepository(Notification::class)
                        ->findBy($criteria, ['createdAt' => 'DESC']);
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = $this->dm->getRepository(Notification::class)->find($notificationId);
        if ($notification) {
            $notification->setIsRead(true);
            $this->dm->flush();
        }
    }

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
