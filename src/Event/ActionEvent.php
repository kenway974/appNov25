<?php

namespace App\Event\Action;

use App\Entity\UserAction;
use Symfony\Contracts\EventDispatcher\Event;

final class UserActionAddedEvent extends Event
{
    public function __construct(private UserAction $userAction)
    {
    }

    public function getUserAction(): UserAction
    {
        return $this->userAction;
    }
}

final class UserActionUpdatedEvent extends Event
{
    public function __construct(private UserAction $userAction)
    {
    }

    public function getUserAction(): UserAction
    {
        return $this->userAction;
    }
}

final class UserActionCompletedEvent extends Event
{
    public function __construct(private UserAction $userAction)
    {
    }

    public function getUserAction(): UserAction
    {
        return $this->userAction;
    }
}
