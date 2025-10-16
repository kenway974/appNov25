<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface;
use App\Entity\User;

class ActionLoggerService
{
    public function __construct(private LoggerInterface $logger) {}

    // ---------------- Actions ----------------
    public function logAddedAction(User $user, string $actionTitle): void
    {
        $this->logger->info("ADDED_ACTION: User {$user->getId()} added action '{$actionTitle}'.");
    }

    public function logUpdatedAction(User $user, string $actionTitle): void
    {
        $this->logger->info("UPDATED_ACTION: User {$user->getId()} updated action '{$actionTitle}'.");
    }

    public function logCompletedAction(User $user, string $actionTitle): void
    {
        $this->logger->info("COMPLETED_ACTION: User {$user->getId()} completed action '{$actionTitle}'.");
    }

}
