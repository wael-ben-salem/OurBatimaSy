<?php
// src/Controller/TeamRoomController.php
namespace App\Controller;

use App\Entity\TeamRoom;
use App\Entity\TeamMember;
use App\Entity\TeamMessage;
use App\Entity\Utilisateur;
use App\Service\TeamNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Repository\TeamRoomRepository;
use App\Repository\TeamMessageRepository;


#[Route('/team-room')]
class TeamRoomController extends AbstractController
{
    #[Route('/list', name: 'app_team_room_list', methods: ['GET'])]
    public function list(TeamRoomRepository $teamRoomRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }

        $rooms = $teamRoomRepository->findRoomsForUser($user);

        return $this->render('team_room/list.html.twig', [
            'rooms' => $rooms
        ]);
    }

    #[Route('/rooms/recent', name: 'app_team_room_recent')]
    public function recentRooms(
        TeamRoomRepository $teamRoomRepository,
        TeamMessageRepository $messageRepository
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return new JsonResponse([]);
        }
    
        $rooms = $teamRoomRepository->findRoomsForUser($user);
        
        return $this->render('team_room/_recent_rooms.html.twig', [
            'rooms' => $rooms,
            'message_repo' => $messageRepository
        ]);
    }
    // src/Controller/TeamRoomController.php

#[Route('/{id}/mark-read', name: 'mark_room_messages_read', methods: ['POST'])]
public function markAsRead(
    TeamRoom $room,
    TeamMessageRepository $messageRepo,
    EntityManagerInterface $em
): JsonResponse {
    $user = $this->getUser();
    $messageRepo->markMessagesAsRead($room, $user);
    
    // Force refresh of unread count
    $unreadCount = $messageRepo->countUnreadMessages($room, $user);
    
    return $this->json([
        'status' => 'success',
        'unreadCount' => $unreadCount
    ]);
}

  // src/Controller/TeamRoomController.php
  #[Route('/rooms/unread-count', name: 'app_team_room_unread_count')]
  public function unreadCount(TeamMessageRepository $messageRepo): Response
  {
      $user = $this->getUser();
      if (!$user instanceof Utilisateur) {
          return new Response('0');
      }
  
      $count = $messageRepo->countTotalUnreadForUser($user);
      return new Response((string) $count);
  }

    #[Route('/{id}/join', name: 'app_team_room_join', methods: ['GET'])]
    public function join(
        TeamRoom $room,
        EntityManagerInterface $em,
        TeamNotificationService $notificationService
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }
        
        if (!$room->hasMember($user)) {
            $member = new TeamMember();
            $member->setRoom($room)
                ->setUser($user)
                ->setJoinedAt(new \DateTime())
                ->setIsActive(true);

            $em->persist($member);
            $em->flush();

            $notificationService->notifyMemberJoinedRoom($room, $user);
        }

        return $this->redirectToRoute('app_team_room_show', ['id' => $room->getId()]);
    }

    #[Route('/{id}/message', name: 'app_team_room_message', methods: ['POST'])]
    #[Security("is_granted('post', room)")]
    public function sendMessage(
        TeamRoom $room,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }

        $message = new TeamMessage();
        $message->setRoom($room)
            ->setSender($user)
            ->setContent($request->request->get('content'));

        $em->persist($message);
        $em->flush();

        return $this->redirectToRoute('app_team_room_show', ['id' => $room->getId()]);
    }
    #[Route('/{id}', name: 'app_team_room_show', methods: ['GET'])]
    public function show(
        TeamRoom $room,
        TeamMessageRepository $messageRepo,
        TeamNotificationService $notificationService,
        EntityManagerInterface $entityManager // 👈 injecte le service ici
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }
    
        if ($room->hasMember($user)) {
            $unreadMessages = $messageRepo->findUnreadMessagesForUser($room, $user);
            
            foreach ($unreadMessages as $message) {
                $message->setIsRead(true);
                $notificationService->notifyMessageSeen($message, $user);
            }
    
            // ✅ utilise l'entity manager injecté
            $entityManager->flush();
        }
    
        return $this->render('team_room/show.html.twig', [
            'room' => $room,
            'messages' => $room->getMessages(),
            'isMember' => $room->hasMember($user),
        ]);
    }
}