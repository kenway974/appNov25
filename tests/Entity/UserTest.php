<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\UserAction;
use App\Entity\UserNeed;
use App\Entity\Notification;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserGettersAndSetters(): void
    {
        $user = new User();

        // Email
        $user->setEmail('test@example.com');
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame('test@example.com', $user->getUserIdentifier());

        // Username
        $user->setUsername('JohnDoe');
        $this->assertSame('JohnDoe', $user->getUsername());

        // Password
        $user->setPassword('securepassword');
        $this->assertSame('securepassword', $user->getPassword());

        // Roles
        $user->setRoles(['ROLE_ADMIN']);
        $roles = $user->getRoles();
        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles); // ROLE_USER is always added

        // isVerified
        $user->setIsVerified(true);
        $this->assertTrue($user->isVerified());

        // CreatedAt
        $now = new \DateTimeImmutable();
        $user->setCreatedAt($now);
        $this->assertSame($now, $user->getCreatedAt());

        // UpdatedAt
        $updated = new \DateTimeImmutable();
        $user->setUpdatedAt($updated);
        $this->assertSame($updated, $user->getUpdatedAt());
    }

    public function testUserCollections(): void
    {
        $user = new User();

        // UserNeed
        $userNeed = new UserNeed();
        $user->addUserNeed($userNeed);
        $this->assertCount(1, $user->getUserNeeds());
        $this->assertSame($user, $userNeed->getUser());

        $user->removeUserNeed($userNeed);
        $this->assertCount(0, $user->getUserNeeds());
        $this->assertNull($userNeed->getUser());

        // UserAction
        $userAction = new UserAction();
        $user->addUserAction($userAction);
        $this->assertCount(1, $user->getUserActions());
        $this->assertSame($user, $userAction->getUser());

        $user->removeUserAction($userAction);
        $this->assertCount(0, $user->getUserActions());
        $this->assertNull($userAction->getUser());

        // Notification
        $notification = new Notification();
        $user->addNotification($notification);
        $this->assertCount(1, $user->getNotifications());
        $this->assertSame($user, $notification->getUser());

        $user->removeNotification($notification);
        $this->assertCount(0, $user->getNotifications());
        $this->assertNull($notification->getUser());
    }

    public function testSerialize(): void
    {
        $user = new User();
        $user->setPassword('secretpassword');

        $serialized = $user->__serialize();
        $this->assertArrayHasKey("\0".User::class."\0password", $serialized);
        $this->assertNotSame('secretpassword', $serialized["\0".User::class."\0password"]); // hashed
    }
}
