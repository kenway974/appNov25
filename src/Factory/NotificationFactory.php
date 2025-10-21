<?php

namespace App\Factory;

use App\Entity\Notification;
use App\Factory\UserFactory;
use App\Factory\UserActionFactory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends PersistentProxyObjectFactory<Notification>
 */
final class NotificationFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Notification::class;
    }

    protected function defaults(): array
    {
        return [
            'title' => self::faker()->sentence(3),
            'message' => self::faker()->optional()->paragraph(),
            'type' => self::faker()->randomElement(['info', 'warning', 'success', 'error']),
            'receivedAt' => self::faker()->dateTimeThisYear(),
            'isRead' => self::faker()->boolean(30), // 30% chance d'être lu
            'user' => UserFactory::new(),
            'userAction' => UserActionFactory::new(),
        ];
    }
}
