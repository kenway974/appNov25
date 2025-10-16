<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface;
use App\Entity\User;

class SecurityLoggerService
{
    public function __construct(private LoggerInterface $logger) {}

    // ---------------- Auth / User ----------------
    public function logRegister(User $user): void
    {
        $this->logger->info("REGISTER: User {$user->getId()} ({$user->getEmail()}) registered.");
    }

    public function logLogin(User $user): void
    {
        $this->logger->info("LOGIN: User {$user->getId()} ({$user->getEmail()}) logged in.");
    }

    public function logLoginFailure(string $email): void
    {
        $this->logger->warning("LOGIN_FAILURE: Failed login attempt for {$email}.");
    }

    public function logLogout(User $user): void
    {
        $this->logger->info("LOGOUT: User {$user->getId()} ({$user->getEmail()}) logged out.");
    }

    public function logPasswordChanged(User $user): void
    {
        $this->logger->info("PASSWORD_CHANGED: User {$user->getId()} changed their password.");
    }

    public function logProfileUpdated(User $user): void
    {
        $this->logger->info("PROFILE_UPDATED: User {$user->getId()} updated profile info.");
    }
}
