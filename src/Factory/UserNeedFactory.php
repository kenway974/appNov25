<?php

namespace App\Factory;

use App\Entity\UserNeed;
use App\Factory\UserFactory;
use App\Factory\NeedFactory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends PersistentProxyObjectFactory<UserNeed>
 */
final class UserNeedFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return UserNeed::class;
    }

    protected function defaults(): array
    {
        return [
            'user' => UserFactory::new(),
            'need' => NeedFactory::new(),
            'priority' => self::faker()->numberBetween(1, 5),
            'score' => self::faker()->numberBetween(0, 100),
            'notes' => self::faker()->optional()->paragraphs(2),
            'lastUpdated' => self::faker()->optional()->dateTimeThisYear(),
        ];
    }

}
