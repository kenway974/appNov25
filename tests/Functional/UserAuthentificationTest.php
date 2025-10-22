<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAuthenticationTest extends WebTestCase
{
    public function testRegistration(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('S’inscrire')->form([
            'registration_form[email]' => 'newuser@example.com',
            'registration_form[password][first]' => 'password123',
            'registration_form[password][second]' => 'password123',
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/dashboard');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Dashboard');
    }

    public function testLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'user1@example.com',
            'password' => '1234',
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/dashboard');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Dashboard');
    }
}
