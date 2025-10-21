<?php

namespace App\Factory;

use App\Entity\Feeling;
use App\Factory\BlockFactory;
use App\Factory\NeedFactory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends PersistentProxyObjectFactory<Feeling>
 */
final class FeelingFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Feeling::class;
    }

    protected function defaults(): array
    {
        return [
            'title' => self::faker()->sentence(3),
            'description' => self::faker()->optional()->paragraph(),
            'emotion' => self::faker()->optional()->randomElement(['Joie', 'Tristesse', 'Colère', 'Peur', 'Surprise', 'Dégoût', 'Culpabilité', 'Honte']),
            'triggers' => self::faker()->optional()->paragraphs(2),
            'color' => self::faker()->optional()->safeColorName(),
            // relations
            'blocks' => BlockFactory::new()->many(0, 2),
            'needs' => NeedFactory::new()->many(0, 2),
        ];
    }
}
