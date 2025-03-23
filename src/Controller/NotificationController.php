<?php

// src/Controller/NotificationController.php
namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notifications')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'notifications_index', methods: ['GET'])]
    public function index(NotificationRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $notifications = $repo->findBy([
            'recipient' => $user,
            'isRead' => false
        ], ['createdAt' => 'DESC']);

        return $this->json($notifications, 200, [], ['groups' => ['notification:read']]);
    }

    #[Route('/{id}/read', name: 'notification_mark_read', methods: ['PUT'])]
    public function markAsRead(Notification $notification, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($notification->getRecipient()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        $notification->markAsRead();
        $em->flush();

        return $this->json(['message' => 'Notification marked as read']);
    }
}