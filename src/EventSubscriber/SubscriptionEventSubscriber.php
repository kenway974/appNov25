<?php

namespace App\EventSubscriber;

use App\Event\Subscription\UserSubscribedEvent;
use App\Event\Subscription\UserSubscriptionCancelledEvent;
use App\Event\Subscription\UserSubscriptionPaymentSuccessEvent;
use App\Event\Subscription\UserSubscriptionPaymentFailedEvent;
use App\Service\Logger\SubscriptionLoggerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Entity\UserSubscription;

final class SubscriptionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private SubscriptionLoggerService $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserSubscribedEvent::class => 'onUserSubscribed',
            UserSubscriptionCancelledEvent::class => 'onUserSubscriptionCancelled',
            UserSubscriptionPaymentSuccessEvent::class => 'onPaymentSuccess',
            UserSubscriptionPaymentFailedEvent::class => 'onPaymentFailed',
        ];
    }

    public function onUserSubscribed(UserSubscribedEvent $event): void
    {
        /** @var UserSubscription $userSubscription */
        $userSubscription = $event->getUserSubscription();
        $this->logger->logSubscribe($userSubscription);
    }

    public function onUserSubscriptionCancelled(UserSubscriptionCancelledEvent $event): void
    {
        /** @var UserSubscription $userSubscription */
        $userSubscription = $event->getUserSubscription();
        $this->logger->logSubscriptionCancelled($userSubscription);
    }

    public function onPaymentFailed(UserSubscriptionPaymentFailedEvent $event): void
    {
        /** @var UserSubscription $userSubscription */
        $userSubscription = $event->getUserSubscription();
        $reason = $event->getReason();
        $this->logger->logPaymentFailed($userSubscription, $reason);
    }
}
