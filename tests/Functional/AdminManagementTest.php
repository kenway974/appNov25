<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class AdminManagementTest extends WebTestCase
{
    public function testManageUsers(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@example.com']);
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/admin/users');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.user-list');
    }

    public function testManageSubscriptions(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@example.com']);
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/admin/subscriptions');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.subscription-list');
    }

    public function testManageContents(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@example.com']);
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/admin/contents');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.content-list');
    }
}
