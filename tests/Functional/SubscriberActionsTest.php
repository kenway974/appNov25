<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class SubscriberActionsTest extends WebTestCase
{
    public function testAccessPersonalizedActions(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user2@example.com']); // abonné
        $client->loginUser($user);

        $crawler = $client->request('GET', '/dashboard/actions');
        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $crawler->filter('.user-action')->count());
    }

    public function testAddActionToDashboard(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user2@example.com']); // abonné
        $client->loginUser($user);

        $crawler = $client->request('GET', '/dashboard/add-action');
        $form = $crawler->selectButton('Ajouter')->form([
            'user_action_form[action]' => 2,
            'user_action_form[userNeed]' => 2,
            'user_action_form[status]' => 'à faire',
        ]);
        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorExists('.user-action');
    }

    public function testContributeToNeedsSatisfaction(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'user2@example.com']);
        $client->loginUser($user);

        $client->request('POST', '/dashboard/user-need/1/increment-score', ['amount' => 10]);
        $this->assertResponseRedirects('/dashboard');
        $client->followRedirect();
        $this->assertSelectorTextContains('.user-need-score', '60'); // supposant que score initial = 50
    }
}
