<?php

namespace App\Tests\Entity;

use App\Entity\UserAction;
use App\Entity\User;
use App\Entity\Action;
use App\Entity\UserNeed;
use App\Entity\Notification;
use PHPUnit\Framework\TestCase;

class UserActionTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $userAction = new UserAction();

        $user = new User();
        $userAction->setUser($user);
        $this->assertSame($user, $userAction->getUser());

        $action = new Action();
        $userAction->setAction($action);
        $this->assertSame($action, $userAction->getAction());

        $userNeed = new UserNeed();
        $userAction->setUserNeed($userNeed);
        $this->assertSame($userNeed, $userAction->getUserNeed());

        $deadline = new \DateTime('2025-12-31');
        $userAction->setDeadline($deadline);
        $this->assertSame($deadline, $userAction->getDeadline());

        $startDate = new \DateTime('2025-01-01');
        $userAction->setStartDate($startDate);
        $this->assertSame($startDate, $userAction->getStartDate());

        $userAction->setFrequency(3);
        $this->assertSame(3, $userAction->getFrequency());

        $userAction->setCompletions(5);
        $this->assertSame(5, $userAction->getCompletions());

        $userAction->setIsChecked(true);
        $this->assertTrue($userAction->isChecked());

        $userAction->setIsRecurring(true);
        $this->assertTrue($userAction->isRecurring());

        $lastUpdate = new \DateTime();
        $userAction->setLastUpdate($lastUpdate);
        $this->assertSame($lastUpdate, $userAction->getLastUpdate());

        $userAction->setStatus('fait');
        $this->assertSame('fait', $userAction->getStatus());

        $notification = new Notification();
        $userAction->setNotification($notification);
        $this->assertSame($notification, $userAction->getNotification());
        $this->assertSame($userAction, $notification->getUserAction());
    }
}
