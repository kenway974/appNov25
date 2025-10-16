<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface;
use App\Entity\User;

class NeedLoggerService
{
    public function __construct(private LoggerInterface $logger) {}

    // ---------------- UserNeeds / Needs ----------------
    public function logAddedNeed(User $user, string $needTitle): void
    {
        $this->logger->info("ADDED_NEED: User {$user->getId()} added need '{$needTitle}'.");
    }

    public function logRemovedNeed(User $user, string $needTitle): void
    {
        $this->logger->info("REMOVED_NEED: User {$user->getId()} removed need '{$needTitle}'.");
    }

    public function logUpdatedNeed(User $user, string $needTitle): void
    {
        $this->logger->info("UPDATED_NEED: User {$user->getId()} updated need '{$needTitle}'.");
    }

    public function logUserNeedScoreUpdated(User $user, string $needTitle, int $score): void
    {
        $this->logger->info("USERNEED_SCORE_UPDATED: User {$user->getId()} updated score of '{$needTitle}' to {$score}.");
    }

}
