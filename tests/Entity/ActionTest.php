<?php

namespace App\Tests\Entity;

use App\Entity\Action;
use App\Entity\UserAction;
use App\Entity\Block;
use App\Entity\Need;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $action = new Action();

        // Test des propriétés simples
        $action->setTitle('Test Action');
        $this->assertSame('Test Action', $action->getTitle());

        $action->setDescription('Description test');
        $this->assertSame('Description test', $action->getDescription());

        $action->setIntension('Intention test');
        $this->assertSame('Intention test', $action->getIntension());

        $action->setIsDoableNow(true);
        $this->assertTrue($action->isDoableNow());

        $action->setDuration(30);
        $this->assertSame(30, $action->getDuration());

        $action->setType('physical');
        $this->assertSame('physical', $action->getType());

        $action->setIcon('fa-solid fa-dumbbell');
        $this->assertSame('fa-solid fa-dumbbell', $action->getIcon());
    }

    public function testUserActionsCollection(): void
    {
        $action = new Action();
        $userAction = new UserAction();

        $this->assertCount(0, $action->getUserActions());

        $action->addUserAction($userAction);
        $this->assertCount(1, $action->getUserActions());
        $this->assertSame($action, $userAction->getAction());

        $action->removeUserAction($userAction);
        $this->assertCount(0, $action->getUserActions());
        $this->assertNull($userAction->getAction());
    }

    public function testBlocksCollection(): void
    {
        $action = new Action();
        $block = new Block();

        $this->assertCount(0, $action->getBlocks());

        $action->addBlock($block);
        $this->assertCount(1, $action->getBlocks());

        $action->removeBlock($block);
        $this->assertCount(0, $action->getBlocks());
    }

    public function testNeedsCollection(): void
    {
        $action = new Action();
        $need = new Need();

        $this->assertCount(0, $action->getNeeds());

        $action->addNeed($need);
        $this->assertCount(1, $action->getNeeds());
        $this->assertTrue($need->getActions()->contains($action));

        $action->removeNeed($need);
        $this->assertCount(0, $action->getNeeds());
        $this->assertFalse($need->getActions()->contains($action));
    }
}
