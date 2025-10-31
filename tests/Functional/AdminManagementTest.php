<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class AdminManagementTest extends WebTestCase
{
    protected function setUp(): void
{
    parent::setUp();

    $client = static::createClient();
    $em = $client->getContainer()->get('doctrine')->getManager();

    $userRepository = $em->getRepository(\App\Entity\User::class);
    $admin = $userRepository->findOneBy(['email' => 'admin@example.com']);

    if (!$admin) {
        $admin = new \App\Entity\User();
        $admin->setEmail('admin@example.com');
        $admin->setPassword('password123'); // pour loginUser, l’encodage n’est pas nécessaire
        $admin->setRoles(['ROLE_ADMIN']);
        $em->persist($admin);
        $em->flush();
    }
}

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
