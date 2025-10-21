<?php

namespace App\Factory;

use App\Entity\UserAction;
use App\Factory\UserFactory;
use App\Factory\ActionFactory;
use App\Factory\UserNeedFactory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends PersistentProxyObjectFactory<UserAction>
 */
final class UserActionFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return UserAction::class;
    }

    protected function defaults(): array
    {
        return [
            'user' => UserFactory::new(),
            'action' => ActionFactory::new(),
            'userNeed' => UserNeedFactory::new(),
            'deadline' => self::faker()->optional()->dateTimeBetween('now', '+1 month'),
            'startDate' => self::faker()->optional()->dateTimeBetween('-1 month', 'now'),
            'frequency' => self::faker()->optional()->numberBetween(1, 7),
            'completions' => self::faker()->optional()->numberBetween(0, 10),
            'isChecked' => self::faker()->boolean(50),
            'isRecurring' => self::faker()->boolean(30),
            'lastUpdate' => self::faker()->optional()->dateTimeThisMonth(),
            'status' => self::faker()->randomElement(['à faire', 'fait']),
        ];
    }
}
