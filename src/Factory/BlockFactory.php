<?php

namespace App\Factory;

use App\Entity\Block;
use App\Entity\Action;
use App\Entity\Feeling;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Block>
 */
final class BlockFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Block::class;
    }

    protected function defaults(): array
    {
        return [
            'title' => self::faker()->sentence(3),
            'description' => self::faker()->optional()->paragraph(),
            'type' => self::faker()->optional()->randomElement(['mental', 'physical', 'social', 'creative']),
            'beliefs' => self::faker()->optional()->paragraphs(2),
            'reframings' => self::faker()->optional()->paragraphs(2),
            'icon' => self::faker()->optional()->randomElement([
                'fa-solid fa-brain',
                'fa-solid fa-dumbbell',
                'fa-solid fa-heart',
                'fa-solid fa-lightbulb',
            ]),
            // relations
            'actions' => ActionFactory::new()->many(0, 2),
            'feelings' => FeelingFactory::new()->many(0, 2),
        ];
    }
}
