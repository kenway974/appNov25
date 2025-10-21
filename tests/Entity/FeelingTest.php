<?php

namespace App\Tests\Entity;

use App\Entity\Feeling;
use App\Entity\Block;
use App\Entity\Need;
use PHPUnit\Framework\TestCase;

class FeelingTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $feeling = new Feeling();

        $feeling->setTitle('Joie');
        $this->assertSame('Joie', $feeling->getTitle());

        $feeling->setDescription('Sentiment agréable');
        $this->assertSame('Sentiment agréable', $feeling->getDescription());

        $feeling->setEmotion('happy');
        $this->assertSame('happy', $feeling->getEmotion());

        $feeling->setTriggers(['musique', 'rencontre']);
        $this->assertSame(['musique', 'rencontre'], $feeling->getTriggers());

        $feeling->setColor('yellow');
        $this->assertSame('yellow', $feeling->getColor());
    }

    public function testBlocksCollection(): void
    {
        $feeling = new Feeling();
        $block = new Block();

        $this->assertCount(0, $feeling->getBlocks());

        $feeling->addBlock($block);
        $this->assertCount(1, $feeling->getBlocks());
        $this->assertTrue($feeling->getBlocks()->contains($block));

        $feeling->removeBlock($block);
        $this->assertCount(0, $feeling->getBlocks());
    }

    public function testNeedsCollection(): void
    {
        $feeling = new Feeling();
        $need = new Need();

        $this->assertCount(0, $feeling->getNeeds());

        $feeling->addNeed($need);
        $this->assertCount(1, $feeling->getNeeds());
        $this->assertTrue($feeling->getNeeds()->contains($need));

        $feeling->removeNeed($need);
        $this->assertCount(0, $feeling->getNeeds());
    }
}
