<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface;
use App\Entity\User;
use App\Entity\Subscription;

class SubscriptionLoggerService
{
    public function __construct(private LoggerInterface $logger) {}

    // ---------------- SUBSCRIPTIONS ----------------

    public function logSubscribed(User $user, Subscription $subscription): void
    {
        $this->logger->info(sprintf(
            "SUBSCRIPTION_STARTED: User #%d subscribed to plan #%d (status=%s, start_date=%s, end_date=%s, recurring=%s).",
            $user->getId(),
            $subscription->getPlan()?->getId() ?? 0,
            $subscription->getStatus() ?? 'N/A',
            $subscription->getStartDate()?->format('Y-m-d H:i:s') ?? 'none',
            $subscription->getEndDate()?->format('Y-m-d H:i:s') ?? 'none',
            $subscription->isRecurring() ? 'true' : 'false'
        ));
    }

    public function logCanceled(User $user, Subscription $subscription): void
    {
        $this->logger->info(sprintf(
            "SUBSCRIPTION_CANCELED: User #%d canceled subscription #%d (plan_id=%d, status=%s, end_date=%s).",
            $user->getId(),
            $subscription->getId(),
            $subscription->getPlan()?->getId() ?? 0,
            $subscription->getStatus() ?? 'N/A',
            $subscription->getEndDate()?->format('Y-m-d H:i:s') ?? 'none'
        ));
    }

    public function logRenewed(User $user, Subscription $subscription): void
    {
        $this->logger->info(sprintf(
            "SUBSCRIPTION_RENEWED: User #%d renewed subscription #%d (plan_id=%d, new_end_date=%s, recurring=%s).",
            $user->getId(),
            $subscription->getId(),
            $subscription->getPlan()?->getId() ?? 0,
            $subscription->getEndDate()?->format('Y-m-d H:i:s') ?? 'none',
            $subscription->isRecurring() ? 'true' : 'false'
        ));
    }

    public function logExpired(User $user, Subscription $subscription): void
    {
        $this->logger->warning(sprintf(
            "SUBSCRIPTION_EXPIRED: User #%d subscription #%d (plan_id=%d) expired at %s.",
            $user->getId(),
            $subscription->getId(),
            $subscription->getPlan()?->getId() ?? 0,
            $subscription->getEndDate()?->format('Y-m-d H:i:s') ?? 'unknown'
        ));
    }

    public function logStatusUpdated(User $user, Subscription $subscription): void
    {
        $this->logger->info(sprintf(
            "SUBSCRIPTION_STATUS_UPDATED: User #%d subscription #%d status changed to '%s'.",
            $user->getId(),
            $subscription->getId(),
            $subscription->getStatus() ?? 'N/A'
        ));
    }

    // ---------------- AUTRE (optionnel) ----------------

    public function logCustom(User $user, string $message): void
    {
        $this->logger->info(sprintf(
            "CUSTOM_SUBSCRIPTION_LOG: User #%d - %s",
            $user->getId(),
            $message
        ));
    }
}
