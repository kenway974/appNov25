<?php

namespace App\Tests\Entity;

use App\Entity\UserNeed;
use App\Entity\User;
use App\Entity\Need;
use App\Entity\UserAction;
use PHPUnit\Framework\TestCase;

class UserNeedTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $userNeed = new UserNeed();

        $user = new User();
        $userNeed->setUser($user);
        $this->assertSame($user, $userNeed->getUser());

        $need = new Need();
        $userNeed->setNeed($need);
        $this->assertSame($need, $userNeed->getNeed());

        $userNeed->setPriority(3);
        $this->assertSame(3, $userNeed->getPriority());

        $userNeed->setScore(75);
        $this->assertSame(75, $userNeed->getScore());

        $notes = ['note1', 'note2'];
        $userNeed->setNotes($notes);
        $this->assertSame($notes, $userNeed->getNotes());

        $lastUpdated = new \DateTime('2025-10-21');
        $userNeed->setLastUpdated($lastUpdated);
        $this->assertSame($lastUpdated, $userNeed->getLastUpdated());

        // Test des UserActions
        $userAction = new UserAction();
        $userNeed->addUserAction($userAction);
        $this->assertTrue($userNeed->getUserActions()->contains($userAction));
        $this->assertSame($userNeed, $userAction->getUserNeed());

        $userNeed->removeUserAction($userAction);
        $this->assertFalse($userNeed->getUserActions()->contains($userAction));
        $this->assertNull($userAction->getUserNeed());
    }
}
