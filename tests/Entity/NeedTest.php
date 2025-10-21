<?php

namespace App\Tests\Entity;

use App\Entity\Need;
use App\Entity\UserNeed;
use App\Entity\Feeling;
use App\Entity\Action;
use PHPUnit\Framework\TestCase;

class NeedTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $need = new Need();

        $need->setTitle('Autonomie');
        $this->assertSame('Autonomie', $need->getTitle());

        $need->setDescription('Besoin de décider par soi-même');
        $this->assertSame('Besoin de décider par soi-même', $need->getDescription());

        $need->setType('psychological');
        $this->assertSame('psychological', $need->getType());

        $need->setFulfilment(['satisfaction', 'réussite']);
        $this->assertSame(['satisfaction', 'réussite'], $need->getFulfilment());

        $need->setIcon('icon-autonomy');
        $this->assertSame('icon-autonomy', $need->getIcon());
    }

    public function testUserNeedsCollection(): void
    {
        $need = new Need();
        $userNeed = $this->createMock(UserNeed::class);

        $this->assertCount(0, $need->getUserNeeds());

        $need->addUserNeed($userNeed);
        $this->assertCount(1, $need->getUserNeeds());
        $this->assertTrue($need->getUserNeeds()->contains($userNeed));

        $need->removeUserNeed($userNeed);
        $this->assertCount(0, $need->getUserNeeds());
    }

    public function testFeelingsCollection(): void
    {
        $need = new Need();
        $feeling = new Feeling();

        $this->assertCount(0, $need->getFeelings());

        $need->addFeeling($feeling);
        $this->assertCount(1, $need->getFeelings());
        $this->assertTrue($need->getFeelings()->contains($feeling));

        $need->removeFeeling($feeling);
        $this->assertCount(0, $need->getFeelings());
    }

    public function testActionsCollection(): void
    {
        $need = new Need();
        $action = new Action();

        $this->assertCount(0, $need->getActions());

        $need->addAction($action);
        $this->assertCount(1, $need->getActions());
        $this->assertTrue($need->getActions()->contains($action));

        $need->removeAction($action);
        $this->assertCount(0, $need->getActions());
    }
}
