<?php

namespace App\Controller;

use App\Entity\PlanifNotifications;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notifications/mark-as-read/{id}', name: 'mark_notification_read')]
public function markAsRead(
    PlanifNotifications $notification, 
    EntityManagerInterface $em, 
    Request $request
): Response {
    $notification->setIsRead(true);
    $em->flush();

    if ($request->isXmlHttpRequest()) {
        return new JsonResponse(['success' => true]);
    }

    $referer = $request->headers->get('referer');
    return $this->redirect($referer ?: $this->generateUrl('app_welcome'));
}


    #[Route('/notifications', name: 'app_notifications')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $notifications = $em->getRepository(PlanifNotifications::class)->findAllByUser($user);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications
        ]);
    }
    // Dans NotificationController.php
#[Route('/notifications/recent-unread', name: 'notification_recent_unread')]
public function recentUnreadNotifications(EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    if (!$user) return new Response('');

    $notifications = $em->getRepository(PlanifNotifications::class)
        ->findBy([
            'recipient' => $user,
            'isRead' => false
        ], ['createdAt' => 'DESC'], 5);

    return $this->render('notification/_recent_items.html.twig', [
        'notifications' => $notifications
    ]);
}
#[Route('/notifications/unread-count', name: 'notification_unread_count')]
public function unreadCount(EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    if (!$user) return new Response('0');

    $count = $em->getRepository(PlanifNotifications::class)
        ->count([
            'recipient' => $user,
            'isRead' => false
        ]);

    return new Response((string) $count);
}


    #[Route('/notifications/recent', name: 'notification_recent')]
    public function recentNotifications(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return new Response('');

        $notifications = $em->getRepository(PlanifNotifications::class)
            ->findBy(['recipient' => $user], ['createdAt' => 'DESC'], 5);

        return $this->render('notification/_recent.html.twig', [
            'notifications' => $notifications
        ]);
    }
}