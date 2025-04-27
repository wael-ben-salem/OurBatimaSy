<?php

namespace App\Controller;

use App\Entity\PlanifNotifications;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notifications/mark-as-read/{id}', name: 'mark_notification_read')]
    public function markAsRead(PlanifNotifications $notification, EntityManagerInterface $em): Response
    {
        $notification->setIsRead(true);
        $em->flush();

        return $this->redirectToRoute('app_plannification_show', [
            'idPlannification' => $notification->getPlannification()->getIdPlannification()
        ]);
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

    #[Route('/notifications/unread-count', name: 'notification_unread_count')]
    public function unreadCount(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return new Response('0');

        $count = $em->getRepository(PlanifNotifications::class)
            ->createQueryBuilder('n')
            ->select('count(n.id)')
            ->where('n.recipient = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return new Response($count);
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
