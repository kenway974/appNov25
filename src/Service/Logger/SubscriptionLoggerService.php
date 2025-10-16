<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface;
use App\Entity\UserSubscription;

class SubscriptionLoggerService
{
    public function __construct(private LoggerInterface $logger) {}

    // ---------------- Subscription / Payment ----------------
    public function logSubscribe(UserSubscription $userSubscription): void
    {
        $user = $userSubscription->getUser();
        $subscriptionTitle = $userSubscription->getSubscription()->getTitle();
        $this->logger->info("SUBSCRIBE: User {$user->getId()} subscribed to '{$subscriptionTitle}'.");
    }

    public function logSubscriptionCancelled(UserSubscription $userSubscription): void
    {
        $user = $userSubscription->getUser();
        $subscriptionTitle = $userSubscription->getSubscription()->getTitle();
        $this->logger->info("SUBSCRIPTION_CANCELLED: User {$user->getId()} cancelled '{$subscriptionTitle}'.");
    }

    public function logPaymentFailed(UserSubscription $userSubscription, string $reason): void
    {
        $user = $userSubscription->getUser();
        $subscriptionTitle = $userSubscription->getSubscription()->getTitle();
        $this->logger->warning("PAYMENT_FAILED: User {$user->getId()} failed payment for '{$subscriptionTitle}'. Reason: {$reason}");
    }
}
