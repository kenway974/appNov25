<?php 

namespace App\EventSubscriber;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment;

class NotificationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private NotificationRepository $repo,
        private Environment $twig
    ) {}

    public function onControllerEvent(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // Si le contrôleur est un callable array [Controller, method]
        if (!is_array($controller)) {
            return;
        }

        $user = $this->security->getUser();
        if ($user) {
            $notifications = $this->repo->findByUser($user);
            // Ajoute les notifications comme variable globale Twig
            $this->twig->addGlobal('notifications', $notifications);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
