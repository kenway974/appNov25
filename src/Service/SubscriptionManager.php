<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Plan;
use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\Subscription\{
    UserSubscribedEvent,
    UserSubscriptionCancelledEvent,
    UserSubscriptionPaymentSuccessEvent,
    UserSubscriptionPaymentFailedEvent
};

class SubscriptionManager
{
    private SubscriptionRepository $subscriptionRepo;
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        SubscriptionRepository $subscriptionRepo,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher
    ) {
        $this->subscriptionRepo = $subscriptionRepo;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Crée un abonnement pour un utilisateur et l’enregistre en base
     */
    public function create(User $user, Plan $plan): Subscription
    {
        $user->setRoles(['ROLE_SUBSCRIBER']);

        $now = new \DateTime();
        $expire = (clone $now)->modify('+1 month');

        $subscription = (new Subscription())
            ->setUser($user)
            ->setPlan($plan)
            ->setStartDate($now)
            ->setExpireDate($expire)
            ->setIsRecurring(true)
            ->setIsActive(true);

        $this->em->persist($subscription);
        $this->em->flush();

        // 🔔 Déclenchement de l'event
        $this->dispatcher->dispatch(new UserSubscribedEvent($subscription));

        return $subscription;
    }

    /**
     * Désactive tous les abonnements expirés
     */
    public function deactivateExpiredSubscriptions(): int
    {
        $now = new \DateTime();
        $subscriptions = $this->subscriptionRepo->findActiveExpired($now);

        foreach ($subscriptions as $subscription) {
            $subscription->setIsActive(false);
            $subscription->getUser()->setRoles(['ROLE_USER']); // rétrograder si nécessaire
        }

        $this->em->flush();

        return count($subscriptions);
    }
}
