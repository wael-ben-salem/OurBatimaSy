<?php
// src/Controller/JitsiController.php

namespace App\Controller;

use App\Entity\TeamMessage;
use App\Entity\TeamRoom;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class JitsiController extends AbstractController
{
    #[Route('/jitsi/join-notification', name: 'jitsi_join_notification', methods: ['POST'])]
public function sendJoinNotification(
    Request $request,
    EntityManagerInterface $em,
    MessageBusInterface $messageBus
): JsonResponse {
    // 1. Vérification de l'authentification
    $user = $this->getUser();
    if (!$user instanceof Utilisateur) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Authentication required'
        ], 401);
    }

    // 2. Récupération des données
    $data = json_decode($request->getContent(), true);
    $roomId = $data['roomId'] ?? null;
    $userName = $data['userName'] ?? $user->getFullName();

    // 3. Vérification de la salle
    $room = $em->getRepository(TeamRoom::class)->find($roomId);
    if (!$room) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Room not found'
        ], 404);
    }

    // 4. Création de la notification pour tous les membres
    $notificationContent = "$userName a rejoint le meeting de l'équipe {$room->getEquipe()->getNom()}";
    
    $message = new TeamMessage();
    $message->setRoom($room);
    $message->setSender($user);
    $message->setContent($notificationContent);
    $message->setIsRead(false);
    $message->setSentAt(new \DateTime());
    $message->setIsSystemMessage(true); // Ajoutez cette propriété à votre entité

    $em->persist($message);
    $em->flush();

    // 5. Réponse JSON
    return new JsonResponse([
        'status' => 'success',
        'message' => 'Notification sent',
        'notification' => [
            'id' => $message->getId(),
            'content' => $notificationContent,
            'sender' => $userName,
            'time' => $message->getSentAt()->format('H:i'),
            'roomId' => $room->getId(),
            'roomName' => $room->getName(),
            'equipeName' => $room->getEquipe()->getNom(),
            'isJitsiNotification' => true
        ]
    ]);
}
}