<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class DashboardTest extends WebTestCase
{
    public function testAccessDashboard(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user1@example.com']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/dashboard');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Dashboard');
        $this->assertSelectorExists('.user-need');
        $this->assertSelectorExists('.user-action');
    }

    public function testListEmotions(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user1@example.com']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/emotions');
        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $crawler->filter('.feeling-item')->count());
    }

    public function testAddNeed(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user1@example.com']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/dashboard/add-need');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'user_need_form[need]' => 1,
            'user_need_form[priority]' => 3,
            'user_need_form[score]' => 50,
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/dashboard');
        $client->followRedirect();
        $this->assertSelectorExists('.user-need');
    }

    public function testSubscriptionFlow(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user1@example.com']);
        $client->loginUser($user);

        // S'abonner
        $crawler = $client->request('GET', '/subscribe');
        $form = $crawler->selectButton('Confirmer')->form(['subscription_form[plan]' => 'premium']);
        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorTextContains('.subscription-status', 'Abonné');

        // Se désabonner
        $client->request('GET', '/unsubscribe');
        $client->followRedirect();
        $this->assertSelectorTextContains('.subscription-status', 'Non abonné');
    }
}
