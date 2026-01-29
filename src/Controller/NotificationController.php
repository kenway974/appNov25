<?php

namespace App\Controller;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notifications')]
class NotificationController extends AbstractController
{
    public function __construct(private NotificationService $notificationService) {}

    #[Route('/user/{userId}', name: 'get_user_notifications', methods: ['GET'])]
    public function getUserNotifications(int $userId): JsonResponse
    {
        $notifications = $this->notificationService->getUserNotifications($userId);
        $data = array_map(fn($n) => [
            'id' => $n->getId(),
            'title' => $n->getTitle(),
            'message' => $n->getMessage(),
            'type' => $n->getType(),
            'isRead' => $n->getIsRead(),
            'createdAt' => $n->getCreatedAt()->format('c'),
            'context' => $n->getContext(),
        ], $notifications);

        return $this->json($data);
    }

    #[Route('/mark-read/{id}', name: 'mark_notification_read', methods: ['POST'])]
    public function markRead(string $id): JsonResponse
    {
        $this->notificationService->markAsRead($id);
        return $this->json(['status' => 'ok']);
    }
}
