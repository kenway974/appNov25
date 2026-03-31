<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAuthentificationTest extends WebTestCase
{
    public function testRegistration(): void
{
    $client = static::createClient();
    $crawler = $client->request('GET', '/register');

    // Vérifie que la page est OK
    $this->assertResponseIsSuccessful();

    // Sélectionne le formulaire avec le bouton exact "M'inscrire"
    $form = $crawler->selectButton("M'inscrire")->form([
        'registration_form[email]' => 'newuser@example.com',
        'registration_form[username]' => 'alimmsa',
        'registration_form[plainPassword]' => 'password123',
        'registration_form[agreeTerms]' => 1,
    ]);

    // Soumet le formulaire
    $client->submit($form);

    // Vérifie la redirection vers le dashboard
    $this->assertResponseRedirects('/dashboard');
    $client->followRedirect();

    // Vérifie que le h1 contient "Dashboard"
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
