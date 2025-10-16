<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\UserAction;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Crée et persiste une nouvelle notification à partir d'une UserAction.
     *
     * @param UserAction|null $userAction Action liée à la notification
     *
     * @return Notification|null
     */
    public function createNotification(?UserAction $userAction = null): ?Notification
    {
        if (!$userAction) {
            return null; // rien à faire si pas d'action
        }

        $notification = new Notification();

        $title = "C'est fait ?";
        $message = "Ne t'en veux pas si ce n'est pas fait, mais ne tarde pas...";
        $type = "actionCheck";

        $notification
            ->setUser($userAction->getUser())
            ->setTitle($title)
            ->setMessage($message)
            ->setType($type)
            ->setReceivedAt(new \DateTimeImmutable())
            ->setIsRead(false)
            ->setUserAction($userAction);

        $this->em->persist($notification);
        $this->em->flush();

        return $notification;
    }
}
