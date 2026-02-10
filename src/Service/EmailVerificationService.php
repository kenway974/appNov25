<?php

namespace App\Service;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class EmailVerificationService
{
    public function __construct(
        private EmailVerifier $emailVerifier
    ) {
    }

    public function verify(Request $request, User $user): void
    {
        $this->emailVerifier->handleEmailConfirmation($request, $user);
        $user->setIsVerified(true);
    }
}
