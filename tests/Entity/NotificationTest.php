<?php

namespace App\Tests\Entity;

use App\Entity\Notification;
use App\Entity\User;
use App\Entity\UserAction;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $notification = new Notification();

        $notification->setTitle('Nouvelle action');
        $this->assertSame('Nouvelle action', $notification->getTitle());

        $notification->setMessage('Vous avez une nouvelle tâche à réaliser.');
        $this->assertSame('Vous avez une nouvelle tâche à réaliser.', $notification->getMessage());

        $notification->setType('info');
        $this->assertSame('info', $notification->getType());

        $now = new \DateTimeImmutable();
        $notification->setReceivedAt($now);
        $this->assertSame($now, $notification->getReceivedAt());

        $notification->setIsRead(true);
        $this->assertTrue($notification->isRead());

        $user = new User();
        $notification->setUser($user);
        $this->assertSame($user, $notification->getUser());

        $userAction = $this->createMock(UserAction::class);
        $notification->setUserAction($userAction);
        $this->assertSame($userAction, $notification->getUserAction());
    }
}
