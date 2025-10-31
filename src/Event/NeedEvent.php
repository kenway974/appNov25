<?php

namespace App\Event;

use App\Entity\User;
use App\Entity\Need;
use Symfony\Contracts\EventDispatcher\Event;

final class NeedEvent extends Event
{
    public function __construct(private User $user, private Need $need) {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function getNeed(): Need
    {
        return $this->need;
    }
}

final class UserNeedScoreUpdatedEvent extends Event
{
    public function __construct(private User $user, private Need $need, private int $score) {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function getNeed(): Need
    {
        return $this->need;
    }

    public function getScore(): int
    {
        return $this->score;
    }
}
