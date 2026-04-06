<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\UserAction;

class UserActionTest extends WebTestCase
{
    public function testCompleteSuccess(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        // 1. Créer un utilisateur
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setPassword('password');

        // 2. Créer une UserAction liée à cet utilisateur
        $userAction = new UserAction();
        $userAction->setUser($user);

        $em = $container->get('doctrine')->getManager();
        $em->persist($user);
        $em->persist($userAction);
        $em->flush();

        // 3. Login
        $client->loginUser($user);

        // 4. Générer token CSRF
        $csrfTokenManager = $container->get('security.csrf.token_manager');
        $token = $csrfTokenManager
            ->getToken('complete_user_action_' . $userAction->getId())
            ->getValue();

        // 5. Appel de la route
        $client->request('POST', '/complete/'.$userAction->getId(), [
            '_token' => $token
        ]);

        // 6. Vérifications
        $this->assertResponseRedirects('/dashboard');
    }
}
