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
        $this->logger->info('Service StripeWebhookHandler appelé pour event: ' . $event->type);

        if ($event->type === 'checkout.session.completed') {
            $this->handleCheckoutCompleted($event);
        }
    }

    private function handleCheckoutCompleted(Event $event): void
    {
        $this->logger->info('Handler final appelé, event bien écouté');

        $session = $event->data->object;

        // sécurité : on ne traite que les subscriptions
        if (($session->mode ?? null) !== 'subscription') {
            $this->logger->warning("Checkout session non subscription, ignore.");
            return;
        }

        // récupère l'email du client Stripe
        $customerEmail = $session->customer_details->email ?? null;
        if (!$customerEmail) {
            $this->logger->error("Aucun email client dans la session Stripe, abort.");
            return;
        }

        // cherche l'utilisateur dans la BDD
        $user = $this->userRepository->findOneBy(['email' => $customerEmail]);
        if (!$user) {
            $this->logger->error("Utilisateur non trouvé pour email $customerEmail, abort.");
            return;
        }

        $subscriptionId = $session->subscription ?? null;

        // -------------------
        // IDÉMPOTENCE
        // -------------------
        // On vérifie si la subscription Stripe existe déjà et est active
        // pour éviter de recréer une subscription si Stripe renvoie le même event deux fois
        $sub = null;
        if ($subscriptionId) {
            $sub = $this->subscriptionRepository->findOneBy(['stripeSubscriptionId' => $subscriptionId]);
            if ($sub && $sub->isActive()) {
                $this->logger->info("Subscription {$subscriptionId} déjà active, event ignoré pour idempotence.");
                return;
            }
        }

        // si pas trouvée par Stripe ID, on essaye de trouver une subscription locale
        if (!$sub) {
            $sub = $this->subscriptionRepository->findOneByUser($user);
        }

        // si toujours pas de subscription, on en crée une nouvelle
        if (!$sub) {
            $sub = new Subscription();
            $user->setSubscription($sub);
        }

        // -------------------
        // RÉCUPÉRATION DU PLAN
        // -------------------
        $amountCents = $session->amount_total ?? null;
        if ($amountCents !== null) {
            $amount = $amountCents / 100; // conversion en euros
            $plan = $this->planRepository->findOneBy(['price' => $amount]);
            if ($plan) {
                $sub->setPlan($plan);
                $this->logger->info("Plan {$plan->getId()} assigné à la subscription.");
            } else {
                $this->logger->warning("Aucun plan trouvé pour le montant $amount €.");
            }
        } else {
            $this->logger->warning("Impossible de récupérer le montant total de la session Stripe.");
        }

        // -------------------
        // ACTIVATION / MISE À JOUR DE LA SUBSCRIPTION
        // -------------------
        $sub->setIsActive(true)
            ->setStatus('Actif')
            ->setStartDate(new \DateTime())
            ->setEndDate((new \DateTime())->modify('+1 month'))
            ->setStripeSubscriptionId($subscriptionId)
            ->setIsRecurring(true)
            ->setTransactionId('stripe_' . $subscriptionId)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        // -------------------
        // AJOUT DU RÔLE SUBSCRIBER
        // -------------------
        $roles = $user->getRoles();
        if (!in_array('ROLE_SUBSCRIBER', $roles)) {
            $roles[] = 'ROLE_SUBSCRIBER';
            $user->setRoles($roles);
            $this->logger->info("✅ ROLE_SUBSCRIBER ajouté à l'utilisateur {$user->getId()}");
        }

        // -------------------
        // PERSISTANCE
        // -------------------
        $this->em->persist($sub);
        $this->em->persist($user);
        $this->em->flush();

        $this->logger->info(
            "✅ Subscription active pour user {$user->getId()} jusqu'au " . $sub->getEndDate()->format('Y-m-d')
        );
    }
}