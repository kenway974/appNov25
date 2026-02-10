<?php

namespace App\Service;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private EmailVerifier $emailVerifier
    ) {
    }

    public function register(User $user, string $plainPassword): void
    {
        // Hash du mot de passe
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $plainPassword)
        );

        // Données métier
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setRoles(['ROLE_USER']);

        // Persistence
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Envoi de l'email de confirmation
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@example.com', 'Tiken Mail Bot'))
                ->to((string) $user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
