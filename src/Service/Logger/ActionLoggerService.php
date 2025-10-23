<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface;
use App\Entity\User;
use App\Entity\UserAction;

class ActionLoggerService
{
    public function __construct(private LoggerInterface $logger) {}

    // ---------------- ACTIONS ----------------

    public function logAddedAction(User $user, UserAction $action): void
    {
        $this->logger->info(sprintf(
            "ADDED_ACTION: User #%d added action '%s' (is_recurring=%s, frequency=%s, deadline=%s).",
            $user->getId(),
            $action->getAction()?->getTitle() ?? 'N/A',
            $action->isRecurring() ? 'true' : 'false',
            $action->getFrequency() ?? 'none',
            $action->getDeadline()?->format('Y-m-d H:i:s') ?? 'none'
        ));
    }

    public function logUpdatedAction(User $user, UserAction $action): void
    {
        $this->logger->info(sprintf(
            "UPDATED_ACTION: User #%d updated action '%s' (status=%s, checked=%s, completions=%d).",
            $user->getId(),
            $action->getAction()?->getTitle() ?? 'N/A',
            $action->getStatus() ?? 'N/A',
            $action->getCompletions() ?? 0
        ));
    }

    public function logCompletedAction(User $user, UserAction $action): void
    {
        $this->logger->info(sprintf(
            "COMPLETED_ACTION: User #%d completed action '%s' (total completions=%d).",
            $user->getId(),
            $action->getAction()?->getTitle() ?? 'N/A',
            $action->getCompletions() ?? 1
        ));
    }

    public function logDeletedAction(User $user, UserAction $action): void
    {
        $this->logger->info(sprintf(
            "DELETED_ACTION: User #%d deleted action '%s'.",
            $user->getId(),
            $action->getAction()?->getTitle() ?? 'N/A'
        ));
    }

    // ---------------- AUTRE (optionnel) ----------------

    public function logCustom(User $user, string $message): void
    {
        $this->logger->info(sprintf(
            "CUSTOM_ACTION_LOG: User #%d - %s",
            $user->getId(),
            $message
        ));
    }
}
