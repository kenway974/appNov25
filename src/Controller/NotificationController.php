<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/notifiaction')]
final class NotificationController extends AbstractController
{
    #[Route(name: 'app_notifications')]
    public function index(
        NotificationRepository $notificationRepo,
        #[CurrentUser] ?User $user = null
    ): Response {
        if (!$user) {
            // Si pas connecté, renvoyer vide ou rediriger
            return $this->render('notification/index.html.twig', [
                'notifications' => [],
            ]);
        }

        // Récupère toutes les notifications pour l'utilisateur connecté
        $notifications = $notificationRepo->findBy(
            ['user' => $user],
            ['receivedAt' => 'DESC'] // optionnel, ordre décroissant
        );

        return $this->render('notification/_modal.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_notification_delete', methods: ['POST','GET'])]
    public function delete(Notification $notification, EntityManagerInterface $em): Response
    {
        // Optionnel : vérifier que l'utilisateur est propriétaire de la notif
        $user = $this->getUser();
        if ($notification->getUser() !== $user) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer cette notification.');
            return $this->redirectToRoute('app_dashboard'); // ou la page précédente
        }

        $em->remove($notification);
        $em->flush();

        $this->addFlash('success', 'Notification supprimée avec succès.');

        // Rediriger vers la page précédente ou vers un tableau de notifications
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/{id}/read', name: 'app_notification_read', methods: ['POST'])]
    public function markAsRead(Notification $notification, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($notification->getUser() !== $user) {
            return $this->json(['success' => false], 403);
        }

        if (!$notification->IsRead()) {
            $notification->setIsRead(true);
            $em->flush();
        }

        return $this->json(['success' => true]);
    }
}
