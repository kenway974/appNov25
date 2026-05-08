<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use App\Repository\PlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Event;

class StripeWebhookHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private SubscriptionRepository $subscriptionRepository,
        private UserRepository $userRepository,
        private PlanRepository $planRepository,
        private EntityManagerInterface $em
    ) {}

    public function handle(Event $event): void
    {
        $this->logger->info('Stripe event reçu: ' . $event->type);

        if ($event->type === 'checkout.session.completed') {
            $this->handleCheckoutCompleted($event);
        }
    }

    /**
     * Gère la fin d'un paiement Stripe (subscription OU paiement unique)
     */
    private function handleCheckoutCompleted(Event $event): void
    {
        $session = $event->data->object;

        $mode = $session->mode ?? null;

        // -------------------
        // SÉCURITÉ MINIMALE
        // -------------------
        $customerEmail = $session->customer_details->email ?? null;
        if (!$customerEmail) {
            $this->logger->error("Email absent dans la session Stripe");
            return;
        }

        $user = $this->userRepository->findOneBy(['email' => $customerEmail]);
        if (!$user) {
            $this->logger->error("Utilisateur introuvable: $customerEmail");
            return;
        }

        // -------------------
        // ROUTAGE PAR TYPE
        // -------------------
        if ($mode === 'subscription') {
            $this->handleSubscription($session, $user);
        } elseif ($mode === 'payment') {
            $this->handleOneShotPayment($session, $user);
        } else {
            $this->logger->warning("Mode Stripe inconnu: $mode");
        }
    }

    /**
     * Gestion des abonnements
     */
    private function handleSubscription($session, $user): void
    {
        $subscriptionId = $session->subscription ?? null;

        // -------------------
        // IDÉMPOTENCE
        // -------------------
        if ($subscriptionId) {
            $existing = $this->subscriptionRepository->findOneBy([
                'stripeSubscriptionId' => $subscriptionId
            ]);

            if ($existing && $existing->isActive()) {
                $this->logger->info("Subscription déjà traitée: $subscriptionId");
                return;
            }
        }

        $sub = $this->getOrCreateSubscription($user);

        // -------------------
        // PLAN
        // -------------------
        $plan = $this->resolvePlanFromSession($session);
        if ($plan) {
            $sub->setPlan($plan);
        }

        // -------------------
        // ACTIVATION
        // -------------------
        $sub->setIsActive(true)
            ->setStatus('Actif')
            ->setStartDate(new \DateTime())
            ->setEndDate((new \DateTime())->modify('+1 month'))
            ->setStripeSubscriptionId($subscriptionId)
            ->setIsRecurring(true)
            ->setTransactionId('stripe_sub_' . $subscriptionId);

        $this->grantSubscriberRole($user);

        $this->save($sub, $user);

        $this->logger->info("Subscription activée pour user {$user->getId()}");
    }

    /**
     * Gestion des paiements uniques
     */
    private function handleOneShotPayment($session, $user): void
    {
        $paymentIntentId = $session->payment_intent ?? null;

        // IDÉMPOTENCE
        $existing = $this->subscriptionRepository->findOneBy([
            'transactionId' => 'stripe_pay_' . $paymentIntentId
        ]);

        if ($existing) {
            $this->logger->info("Paiement déjà traité: $paymentIntentId");
            return;
        }

        $sub = new Subscription();
        $user->setSubscription($sub);

        // PLAN
        $plan = $this->resolvePlanFromSession($session);
        if ($plan) {
            $sub->setPlan($plan);
        }

        // ACTIVATION (paiement unique)
        $sub->setIsActive(true)
            ->setStatus('Actif')
            ->setStartDate(new \DateTime())
            ->setEndDate((new \DateTime())->modify('+12 months')) // à adapter selon ton plan
            ->setIsRecurring(false)
            ->setTransactionId('stripe_pay_' . $paymentIntentId);

        $this->grantSubscriberRole($user);

        $this->save($sub, $user);

        $this->logger->info("Paiement unique validé pour user {$user->getId()}");
    }

    /**
     * Récupère ou crée une subscription
     */
    private function getOrCreateSubscription($user): Subscription
    {
        $sub = $this->subscriptionRepository->findOneByUser($user);

        if (!$sub) {
            $sub = new Subscription();
            $user->setSubscription($sub);
        }

        return $sub;
    }

    /**
     * Résout le plan depuis la session Stripe
     */
    private function resolvePlanFromSession($session)
    {
        $amountCents = $session->amount_total ?? null;

        if ($amountCents === null) {
            return null;
        }

        $amount = $amountCents / 100;

        return $this->planRepository->findOneBy(['price' => $amount]);
    }

    /**
     * Ajoute le rôle subscriber
     */
    private function grantSubscriberRole($user): void
    {
        $roles = $user->getRoles();

        if (!in_array('ROLE_SUBSCRIBER', $roles)) {
            $roles[] = 'ROLE_SUBSCRIBER';
            $user->setRoles($roles);
        }
    }

    /**
     * Sauvegarde en base
     */
    private function save($sub, $user): void
    {
        $this->em->persist($sub);
        $this->em->persist($user);
        $this->em->flush();
    }
}