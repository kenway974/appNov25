<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\Plan;
use App\Repository\UserRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\PlanRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class StripeWebhookTest extends WebTestCase
{
    private $client;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private SubscriptionRepository $subscriptionRepository;
    private PlanRepository $planRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get(EntityManagerInterface::class);
        $this->userRepository = $this->client->getContainer()->get(UserRepository::class);
        $this->subscriptionRepository = $this->client->getContainer()->get(SubscriptionRepository::class);
        $this->planRepository = $this->client->getContainer()->get(PlanRepository::class);

        parent::setUp();
    }

    public function testCheckoutSessionCompletedWebhook(): void
    {
        // -------------------
        // Création d'un utilisateur de test
        // -------------------
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setUsername('testuser');
        $user->setPassword('testmdp');
        $user->setRoles(['ROLE_USER']);
        $user->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($user);

        // -------------------
        // Création d'un plan correspondant à 50€
        // -------------------
        $plan = new Plan();
        $plan->setPrice(50); // correspond à amount_total = 5000 cents
        $plan->setTitle('Plan Test');
        $plan->setDescription('Plan Test');
        $plan->setDuration(30);
        $plan->setCreatedAt(new \DateTimeImmutable());
        $plan->setUpdatedAt(new \DateTimeImmutable());
        $this->em->persist($plan);

        $this->em->flush();

        // -------------------
        // Payload webhook simulé
        // -------------------
        $payload = [
            'id' => 'evt_test123',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'mode' => 'subscription',
                    'customer_details' => ['email' => 'testuser@example.com'],
                    'subscription' => 'sub_test123',
                    'amount_total' => 5000 // 50€
                ]
            ]
        ];

        $payloadJson = json_encode($payload);

        // -------------------
        // Bypass Stripe signature pour le test
        // -------------------
        $sigHeader = 't=123,v1=fakesignature';

        // -------------------
        // Appel POST vers le webhook
        // -------------------
        $this->client->request(
            'POST',
            '/webhook',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_STRIPE_SIGNATURE' => $sigHeader],
            $payloadJson
        );

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Le webhook doit répondre 200');
        $this->assertEquals('OK', $response->getContent(), 'Le webhook doit renvoyer "OK"');

        // -------------------
        // Vérification utilisateur et rôle
        // -------------------
        $userFromDb = $this->userRepository->findOneBy(['email' => 'testuser@example.com']);
        $this->assertContains('ROLE_SUBSCRIBER', $userFromDb->getRoles(), 'L’utilisateur doit avoir ROLE_SUBSCRIBER');

        // -------------------
        // Vérification subscription
        // -------------------
        $subscription = $this->subscriptionRepository->findOneBy(['stripeSubscriptionId' => 'sub_test123']);
        $this->assertNotNull($subscription, 'La subscription doit exister');
        $this->assertTrue($subscription->isActive(), 'La subscription doit être active');
        $this->assertEquals('Actif', $subscription->getStatus());
        $this->assertEquals($userFromDb->getId(), $subscription->getUser()->getId());
        $this->assertEquals($plan->getId(), $subscription->getPlan()->getId());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Supprimer les données test
        $this->em->createQuery('DELETE FROM App\Entity\Subscription')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\User')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Plan')->execute();
    }
}