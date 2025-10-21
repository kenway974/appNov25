<?php

namespace App\Tests\Entity;

use App\Entity\Block;
use App\Entity\Action;
use App\Entity\Feeling;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $block = new Block();

        $block->setTitle('Titre Test');
        $this->assertSame('Titre Test', $block->getTitle());

        $block->setDescription('Description Test');
        $this->assertSame('Description Test', $block->getDescription());

        $block->setType('mental');
        $this->assertSame('mental', $block->getType());

        $block->setBeliefs(['croyance1', 'croyance2']);
        $this->assertSame(['croyance1', 'croyance2'], $block->getBeliefs());

        $block->setReframings(['reformulation1']);
        $this->assertSame(['reformulation1'], $block->getReframings());

        $block->setIcon('fa-solid fa-brain');
        $this->assertSame('fa-solid fa-brain', $block->getIcon());
    }

    public function testActionsCollection(): void
    {
        $block = new Block();
        $action = new Action();

        $this->assertCount(0, $block->getActions());

        $block->addAction($action);
        $this->assertCount(1, $block->getActions());
        $this->assertTrue($action->getBlocks()->contains($block));

        $block->removeAction($action);
        $this->assertCount(0, $block->getActions());
        $this->assertFalse($action->getBlocks()->contains($block));
    }

    public function testFeelingsCollection(): void
    {
        $block = new Block();
        $feeling = new Feeling();

        $this->assertCount(0, $block->getFeelings());

        $block->addFeeling($feeling);
        $this->assertCount(1, $block->getFeelings());
        $this->assertTrue($feeling->getBlocks()->contains($block));

        $block->removeFeeling($feeling);
        $this->assertCount(0, $block->getFeelings());
        $this->assertFalse($feeling->getBlocks()->contains($block));
    }
}
