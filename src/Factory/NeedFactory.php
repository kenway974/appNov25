<?php

namespace App\Factory;

use App\Entity\Need;
use App\Factory\ActionFactory;
use App\Factory\FeelingFactory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends PersistentProxyObjectFactory<Need>
 */
final class NeedFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Need::class;
    }

    protected function defaults(): array
    {
        return [
            'title' => self::faker()->sentence(3),
            'description' => self::faker()->optional()->paragraph(),
            'type' => self::faker()->optional()->randomElement([
                'Croissance', 'Lien', 'Estime', 'Physique', 'Relationnel', 'Sécurité', 'Sens'
            ]),
            'fulfilment' => self::faker()->optional()->paragraphs(2),
            'icon' => self::faker()->optional()->randomElement([
                'fa-solid fa-brain',
                'fa-solid fa-dumbbell',
                'fa-solid fa-heart',
                'fa-solid fa-lightbulb',
            ]),
            // relations
            'actions' => ActionFactory::new()->many(0, 3),
            'feelings' => FeelingFactory::new()->many(0, 2),
        ];
    }
}
