<?php

namespace App\EventSubscriber;

use App\Event\Need\UserNeedAddedEvent;
use App\Event\Need\UserNeedRemovedEvent;
use App\Event\Need\UserNeedUpdatedEvent;
use App\Event\Need\UserNeedScoreUpdatedEvent;
use App\Service\Logger\NeedLoggerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NeedEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private NeedLoggerService $loggerService) {}

    public static function getSubscribedEvents(): array
    {
        return [
            UserNeedAddedEvent::class => 'onUserNeedAdded',
            UserNeedRemovedEvent::class => 'onUserNeedRemoved',
            UserNeedUpdatedEvent::class => 'onUserNeedUpdated',
            UserNeedScoreUpdatedEvent::class => 'onUserNeedScoreUpdated',
        ];
    }

    public function onUserNeedAdded(UserNeedAddedEvent $event): void
    {
        $this->loggerService->logAddedNeed($event->getUser(), $event->getNeed()->getTitle());
    }

    public function onUserNeedRemoved(UserNeedRemovedEvent $event): void
    {
        $this->loggerService->logRemovedNeed($event->getUser(), $event->getNeed()->getTitle());
    }

    public function onUserNeedUpdated(UserNeedUpdatedEvent $event): void
    {
        $this->loggerService->logUpdatedNeed($event->getUser(), $event->getNeed()->getTitle());
    }

    public function onUserNeedScoreUpdated(UserNeedScoreUpdatedEvent $event): void
    {
        $this->loggerService->logUserNeedScoreUpdated(
            $event->getUser(),
            $event->getNeed()->getTitle(),
            $event->getScore()
        );
    }
}
