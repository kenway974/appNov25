<?php

namespace App\Event;

use App\Entity\UserAction;
use Symfony\Contracts\EventDispatcher\Event;

final class ActionEvent extends Event
{
    public function __construct(private UserAction $userAction)
    {
    }

    public function getUserAction(): UserAction
    {
        return $this->userAction;
    }
}

