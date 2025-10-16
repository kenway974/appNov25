<?php

namespace App\EventSubscriber;

use App\Entity\UserAction;
use App\Event\Action\UserActionAddedEvent;
use App\Event\Action\UserActionUpdatedEvent;
use App\Event\Action\UserActionCompletedEvent;
use App\Service\Logger\ActionLoggerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private ActionLoggerService $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserActionAddedEvent::class => 'onActionAdded',
            UserActionUpdatedEvent::class => 'onActionUpdated',
            UserActionCompletedEvent::class => 'onActionCompleted',
        ];
    }

    public function onActionAdded(UserActionAddedEvent $event): void
    {
        /** @var UserAction $userAction */
        $userAction = $event->getUserAction();
        $this->logger->logAddedAction($userAction->getUser(), $userAction->getAction()->getTitle());
    }

    public function onActionUpdated(UserActionUpdatedEvent $event): void
    {
        /** @var UserAction $userAction */
        $userAction = $event->getUserAction();
        $this->logger->logUpdatedAction($userAction->getUser(), $userAction->getAction()->getTitle());
    }

    public function onActionCompleted(UserActionCompletedEvent $event): void
    {
        /** @var UserAction $userAction */
        $userAction = $event->getUserAction();
        $this->logger->logCompletedAction($userAction->getUser(), $userAction->getAction()->getTitle());
    }
}
