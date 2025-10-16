<?php

namespace App\EventSubscriber;

use App\Service\Logger\SecurityLoggerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Core\Event\PasswordAuthenticatedEvent;

class SecurityEventSubscriber implements EventSubscriberInterface
{
    private SecurityLoggerService $logger;

    public function __construct(SecurityLoggerService $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LoginFailureEvent::class => 'onLoginFailure',
            LogoutEvent::class => 'onLogout',
            // PasswordAuthenticatedEvent::class => 'onPasswordChanged',
            // Ajouter ici ton event custom pour l'inscription si nécessaire
            // UserRegisteredEvent::class => 'onUserRegistered',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $this->logger->logLogin($user);
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $exception = $event->getException();
        $this->logger->logLoginFailure($exception);
    }

    public function onLogout(LogoutEvent $event): void
    {
        $token = $event->getToken();
        if ($token) {
            $user = $token->getUser();
            $this->logger->logLogout($user);
        }
    }

    // public function onPasswordChanged(): void
    // {
    //     $user = $event->getUser();
    //     $this->logger->logPasswordChanged($user);
    // }

    // public function onUserRegistered(UserRegisteredEvent $event): void
    // {
    //     $this->logger->logRegister($event->getUser());
    // }
}
