<?php
// src/Controller/NotificationController.php
namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notifications')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'notifications_index')]
    public function index(NotificationRepository $repo): Response
    {
        $user = $this->getUser();
        $notifications = $repo->findByRecipient($user);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications
        ]);
    }

    #[Route('/{id}/read', name: 'notification_mark_read')]
    public function markAsRead(Notification $notification, EntityManagerInterface $em): Response
    {
        $notification->markAsRead();
        $em->flush();
        return $this->redirectToRoute('notifications_index');
    }
}