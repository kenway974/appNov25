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
        $need = new Need();
        $date = new \DateTime('2025-10-21');
        $notes = ['note1', 'note2'];

        $userNeed->setUser($user);
        $userNeed->setNeed($need);
        $userNeed->setPriority(3);
        $userNeed->setScore(75);
        $userNeed->setNotes($notes);
        $userNeed->setLastUpdated($date);

        $this->assertSame($user, $userNeed->getUser());
        $this->assertSame($need, $userNeed->getNeed());
        $this->assertSame(3, $userNeed->getPriority());
        $this->assertSame(75, $userNeed->getScore());
        $this->assertSame($notes, $userNeed->getNotes());
        $this->assertSame($date, $userNeed->getLastUpdated());
    }

    public function testAddAndRemoveUserAction(): void
    {
        $userNeed = new UserNeed();
        $userAction = new UserAction();

        $userNeed->addUserAction($userAction);

        $this->assertCount(1, $userNeed->getUserActions());
        $this->assertContains($userAction, $userNeed->getUserActions());
        $this->assertSame($userNeed, $userAction->getUserNeed());

        $userNeed->removeUserAction($userAction);

        $this->assertCount(0, $userNeed->getUserActions());
        $this->assertNotContains($userAction, $userNeed->getUserActions());
        $this->assertNull($userAction->getUserNeed());
    }

    public function testMaxActions(): void
    {
        $userNeed = new UserNeed();

        // Ajout de 3 actions -> OK
        for ($i = 0; $i < 3; $i++) {
            $userNeed->addUserAction(new UserAction());
        }

        $this->assertCount(3, $userNeed->getUserActions());

        // Une 4e doit lever une exception
        $this->expectException(\LogicException::class);
        $userNeed->addUserAction(new UserAction());
    }

    public function testTooMuchActions(): void
    {
        $userNeed = new UserNeed();

        // Ajout de 3 actions -> OK
        for ($i = 0; $i < 4; $i++) {
            $userNeed->addUserAction(new UserAction());
        }

        $this->assertCount(3, $userNeed->getUserActions());

        // Une 4e doit lever une exception
        $this->expectException(\LogicException::class);
        $userNeed->addUserAction(new UserAction());
    }
}
