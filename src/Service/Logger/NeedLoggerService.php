<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface;
use App\Entity\User;
use App\Entity\UserNeed;

class NeedLoggerService
{
    public function __construct(private LoggerInterface $logger) {}

    // ---------------- USER NEEDS ----------------

    public function logAddedNeed(User $user, UserNeed $need): void
    {
        $this->logger->info(sprintf(
            "ADDED_NEED: User #%d added need '%s' (priority=%d, score=%d).",
            $user->getId(),
            $need->getNeed()?->getTitle() ?? 'N/A',
            $need->getPriority() ?? 0,
        ));
    }

    public function logUpdatedNeed(User $user, UserNeed $need): void
    {
        $this->logger->info(sprintf(
            "UPDATED_NEED: User #%d updated need '%s' (priority=%d, score=%d, last_updated=%s).",
            $user->getId(),
            $need->getNeed()?->getTitle() ?? 'N/A',
            $need->getPriority() ?? 0,
            $need->getLastUpdated()?->format('Y-m-d H:i:s') ?? 'none'
        ));
    }

    public function logRemovedNeed(User $user, UserNeed $need): void
    {
        $this->logger->info(sprintf(
            "REMOVED_NEED: User #%d removed need '%s'.",
            $user->getId(),
            $need->getNeed()?->getTitle() ?? 'N/A'
        ));
    }

    public function logUserNeedScoreUpdated(User $user, UserNeed $need): void
    {
        $this->logger->info(sprintf(
            "USERNEED_SCORE_UPDATED: User #%d updated score of '%s' to %d.",
            $user->getId(),
            $need->getNeed()?->getTitle() ?? 'N/A',
            $need->getScore() ?? 0
        ));
    }

    // ---------------- AUTRE (optionnel) ----------------

    public function logCustom(User $user, string $message): void
    {
        $this->logger->info(sprintf(
            "CUSTOM_NEED_LOG: User #%d - %s",
            $user->getId(),
            $message
        ));
    }
}
