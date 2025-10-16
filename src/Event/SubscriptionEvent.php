<?php

namespace App\Event\Subscription;

use App\Entity\User;
use App\Entity\Plan;
use App\Entity\Subscription;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event déclenché lorsqu'un utilisateur crée un Subscription (s'abonne à un plan)
 */
final class UserSubscribedEvent extends Event
{
    public function __construct(private Subscription $subscription)
    {
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function getUser(): User
    {
        return $this->subscription->getUser();
    }

    public function getPlan(): Plan
    {
        return $this->subscription->getPlan();
    }
}

/**
 * Event déclenché lorsqu'un utilisateur annule son Subscription
 */
final class UserSubscriptionCancelledEvent extends Event
{
    public function __construct(private Subscription $subscription)
    {
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function getUser(): User
    {
        return $this->subscription->getUser();
    }

    public function getPlan(): Plan
    {
        return $this->subscription->getPlan();
    }
}

/**
 * Event déclenché lorsqu’un paiement est réussi pour un Subscription
 */
final class UserSubscriptionPaymentSuccessEvent extends Event
{
    public function __construct(private Subscription $subscription, private float $amount)
    {
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function getUser(): User
    {
        return $this->subscription->getUser();
    }

    public function getPlan(): Plan
    {
        return $this->subscription->getPlan();
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}

/**
 * Event déclenché lorsqu’un paiement échoue pour un Subscription
 */
final class UserSubscriptionPaymentFailedEvent extends Event
{
    public function __construct(private Subscription $subscription, private float $amount, private string $reason)
    {
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function getUser(): User
    {
        return $this->subscription->getUser();
    }

    public function getPlan(): Plan
    {
        return $this->subscription->getPlan();
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
