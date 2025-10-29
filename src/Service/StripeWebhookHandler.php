<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Repository\UserRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Event;

class StripeWebhookHandler
{
    private LoggerInterface $logger;
    private UserRepository $userRepository;
    private SubscriptionRepository $subscriptionRepository;
    private EntityManagerInterface $em;

    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository,
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $em
    ) {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->em = $em;
    }

    /**
     * Main handler for Stripe events
     */
    public function handle(Event $event): void
    {
        $this->logger->info('Stripe Event Received: ' . $event->type);

        switch ($event->type) {

            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event);
                break;

            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionCanceled($event);
                break;
        }
    }

    private function handleCheckoutCompleted(Event $event): void
    {
        $session = $event->data->object;

        if ($session->mode !== 'subscription') {
            return;
        }

        $customerId = $session->customer;
        $subscriptionId = $session->subscription;

        $user = $this->userRepository->findOneBy(['stripeCustomerId' => $customerId]);

        if (!$user) {
            $this->logger->error("User not found for stripeCustomerId: " . $customerId);
            return;
        }

        $subscription = new Subscription();
        $subscription->setUser($user);
        $subscription->setStripeSubscriptionId($subscriptionId);
        $subscription->setStatus('active');

        $this->em->persist($subscription);

        $roles = $user->getRoles();
        if (!in_array('ROLE_SUBSCRIBER', $roles)) {
            $roles[] = 'ROLE_SUBSCRIBER';
            $user->setRoles($roles);
            $this->logger->info("✅ ROLE_SUBSCRIBER added to user {$user->getId()}");
        }

        $this->em->flush();
        $this->logger->info("✅ Subscription created for user {$user->getId()}");
    }

    private function handleSubscriptionUpdated(Event $event): void
    {
        $subscriptionData = $event->data->object;

        $subscription = $this->subscriptionRepository->findOneBy([
            'stripeSubscriptionId' => $subscriptionData->id,
        ]);

        if (!$subscription) {
            $this->logger->warning("Subscription not found: " . $subscriptionData->id);
            return;
        }

        $status = $subscriptionData->status;
        $subscription->setStatus($status);

        $user = $subscription->getUser();
        $roles = $user->getRoles();

        // 🔹 Ajouter ou retirer ROLE_SUBSCRIBER selon le status
        if (in_array($status, ['active', 'trialing'])) {
            if (!in_array('ROLE_SUBSCRIBER', $roles)) {
                $roles[] = 'ROLE_SUBSCRIBER';
                $user->setRoles($roles);
                $this->logger->info("🔄 ROLE_SUBSCRIBER added to user {$user->getId()}");
            }
        } else {
            if (in_array('ROLE_SUBSCRIBER', $roles)) {
                $roles = array_filter($roles, fn($r) => $r !== 'ROLE_SUBSCRIBER');
                $user->setRoles(array_values($roles));
                $this->logger->info("🔄 ROLE_SUBSCRIBER removed from user {$user->getId()}");
            }
        }

        $this->em->flush();
        $this->logger->info("🔄 Subscription updated: {$subscriptionData->id} ({$status})");
    }

    private function handleSubscriptionCanceled(Event $event): void
    {
        $subscriptionData = $event->data->object;

        $subscription = $this->subscriptionRepository->findOneBy([
            'stripeSubscriptionId' => $subscriptionData->id,
        ]);

        if (!$subscription) {
            $this->logger->warning("Subscription not found: " . $subscriptionData->id);
            return;
        }

        $subscription->setStatus('canceled');

        $user = $subscription->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_SUBSCRIBER', $roles)) {
            $roles = array_filter($roles, fn($r) => $r !== 'ROLE_SUBSCRIBER');
            $user->setRoles(array_values($roles));
            $this->logger->info("❌ ROLE_SUBSCRIBER removed from user {$user->getId()}");
        }

        $this->em->flush();
        $this->logger->info("❌ Subscription canceled: {$subscriptionData->id}");
    }
}
