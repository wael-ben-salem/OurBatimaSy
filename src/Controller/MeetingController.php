<?php
// src/Controller/MeetingController.php

namespace App\Controller;

use App\Entity\TeamMessage;
use App\Entity\TeamRoom;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MeetingController extends AbstractController
{
    #[Route('/meeting/join-notification', name: 'meeting_join_notification', methods: ['POST'])]
    public function sendJoinNotification(
        Request $request,
        EntityManagerInterface $em,
        HubInterface $hub
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $data = json_decode($request->getContent(), true);
        $roomId = $data['roomId'] ?? null;
        $userName = $data['userName'] ?? $user->getFirstName();

        $room = $em->getRepository(TeamRoom::class)->find($roomId);
        if (!$room) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Salle de réunion introuvable'
            ], 404);
        }

        // Créer le message de notification
        $message = new TeamMessage();
        $message->setRoom($room);
        $message->setSender($user);
        $message->setContent("$userName a rejoint le meeting");
        $message->setIsRead(false);
        $message->setSentAt(new \DateTime());

        $em->persist($message);
        $em->flush();

        // Publier la notification via Mercure
        $update = new Update(
            ['/meetings/room-'.$room->getId()],
            json_encode([
                'type' => 'meeting_join',
                'roomId' => $room->getId(),
                'message' => $message->getContent(),
                'sender' => $userName,
                'time' => $message->getSentAt()->format('H:i'),
                'unreadCount' => $em->getRepository(TeamMessage::class)
                    ->countUnreadMessages($room, $user)
            ])
        );

        $hub->publish($update);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Notification envoyée',
            'notification' => [
                'content' => $message->getContent(),
                'sender' => $userName,
                'time' => $message->getSentAt()->format('H:i')
            ]
        ]);
    }
}