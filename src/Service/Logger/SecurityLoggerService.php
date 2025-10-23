<?php

namespace App\Service\Logger;

use Psr\Log\LoggerInterface;
use App\Entity\User;

class SecurityLoggerService
{
    public function __construct(private LoggerInterface $logger) {}

    // ---------------- AUTH / USER ----------------

    public function logRegister(User $user): void
    {
        $this->logger->info(sprintf(
            "REGISTER: User #%d (%s) registered at %s.",
            $user->getId(),
            $user->getEmail(),
            (new \DateTime())->format('Y-m-d H:i:s')
        ));
    }

    public function logLogin(User $user, ?string $ip = null): void
    {
        $this->logger->info(sprintf(
            "LOGIN: User #%d (%s) logged in at %s%s.",
            $user->getId(),
            $user->getEmail(),
            (new \DateTime())->format('Y-m-d H:i:s'),
            $ip ? " from IP {$ip}" : ''
        ));
    }

    public function logLoginFailure(string $email, ?string $ip = null): void
    {
        $this->logger->warning(sprintf(
            "LOGIN_FAILURE: Failed login attempt for %s at %s%s.",
            $email,
            (new \DateTime())->format('Y-m-d H:i:s'),
            $ip ? " from IP {$ip}" : ''
        ));
    }

    public function logLogout(User $user, ?string $ip = null): void
    {
        $this->logger->info(sprintf(
            "LOGOUT: User #%d (%s) logged out at %s%s.",
            $user->getId(),
            $user->getEmail(),
            (new \DateTime())->format('Y-m-d H:i:s'),
            $ip ? " from IP {$ip}" : ''
        ));
    }

    public function logPasswordChanged(User $user): void
    {
        $this->logger->info(sprintf(
            "PASSWORD_CHANGED: User #%d (%s) changed password at %s.",
            $user->getId(),
            $user->getEmail(),
            (new \DateTime())->format('Y-m-d H:i:s')
        ));
    }

    public function logProfileUpdated(User $user): void
    {
        $this->logger->info(sprintf(
            "PROFILE_UPDATED: User #%d (%s) updated profile info at %s.",
            $user->getId(),
            $user->getEmail(),
            (new \DateTime())->format('Y-m-d H:i:s')
        ));
    }

    // ---------------- AUTRE (optionnel) ----------------

    public function logCustom(User $user, string $message): void
    {
        $this->logger->info(sprintf(
            "CUSTOM_SECURITY_LOG: User #%d (%s) - %s",
            $user->getId(),
            $user->getEmail(),
            $message
        ));
    }
}
