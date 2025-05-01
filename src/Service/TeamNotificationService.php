<?php
// src/Service/TeamNotificationService.php
namespace App\Service;

use App\Entity\Equipe;
use App\Entity\TeamRoom;
use App\Entity\TeamMessage;

use App\Entity\Utilisateur;
use App\Entity\TeamMember;
use App\Entity\PlanifNotifications;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TeamNotificationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger
    ) {}
    // src/Service/TeamNotificationService.php
public function sendTeamCreationNotifications(Equipe $equipe): void
{
    $members = $this->getAllTeamMembers($equipe);
    $teamName = $equipe->getNom();

    foreach ($members as $member) {
        // Vérifiez que le membre est bien une entité gérée
        if (!$this->em->contains($member)) {
            $member = $this->em->merge($member);
        }

        $notification = new PlanifNotifications();
        $notification->setRecipient($member)
            ->setMessage("Vous avez été ajouté à l'équipe \"$teamName\"")
            ->setIsRead(false)
            ->setCreatedAt(new \DateTime());

        $this->em->persist($notification);
    }
    
    $this->em->flush();
}

public function sendRoomInvitation(Equipe $equipe, TeamRoom $room): void
{
    $members = $this->getAllTeamMembers($equipe);
    $teamName = $equipe->getNom();
    $roomName = $room->getName();

    foreach ($members as $member) {
        // Vérifiez que le membre est bien une entité gérée
        if (!$this->em->contains($member)) {
            $member = $this->em->merge($member);
        }

        $notification = new PlanifNotifications();
        $notification->setRecipient($member)
            ->setMessage("Vous avez été invité au salon \"$roomName\" de l'équipe \"$teamName\"")
            ->setIsRead(false)
            ->setCreatedAt(new \DateTime());

        $this->em->persist($notification);
    }
    
    $this->em->flush();
}
public function notifyMemberJoinedRoom(TeamRoom $room, Utilisateur $user): void
{
    // S'assurer que l'entity manager est disponible
    foreach ($room->getMembers() as $member) {
        if ($member->getUser() !== $user) {
            $recipient = $member->getUser();
            
            // Vérifier et merger si nécessaire
            if (!$this->em->contains($recipient)) {
                $recipient = $this->em->merge($recipient);
            }

            $notification = new PlanifNotifications();
            $notification->setRecipient($recipient)
                ->setMessage(sprintf(
                    "%s a rejoint le salon \"%s\" de l'équipe \"%s\"",
                    $user->getFullName(),
                    $room->getName(),
                    $room->getEquipe()->getNom()
                ))
                ->setIsRead(false)
                ->setCreatedAt(new \DateTime());

            $this->em->persist($notification);
        }
    }
    $this->em->flush();
}

    public function addAllTeamMembersToRoom(Equipe $equipe, TeamRoom $room): void
    {
        $members = $this->getAllTeamMembers($equipe);

        foreach ($members as $user) {
            if (!$room->hasMember($user)) {
                $member = new TeamMember();
                $member->setRoom($room)
                    ->setUser($user)
                    ->setJoinedAt(new \DateTime())
                    ->setIsActive(true);

                $this->em->persist($member);
                $this->em->flush();
                $this->em->clear();
            }
        }
    }

    private function getAllTeamMembers(Equipe $equipe): array
    {
        $members = [];

        if ($equipe->getConstructeur()) {
            $members[] = $equipe->getConstructeur()->getConstructeur();
        }

        if ($equipe->getGestionnairestock()) {
            $members[] = $equipe->getGestionnairestock()->getGestionnairestock();
        }

        foreach ($equipe->getArtisans() as $artisan) {
            $members[] = $artisan->getArtisan();
        }

        return array_filter($members);
    }
    public function notifyMessageSeen(TeamMessage $message, Utilisateur $user): void
{
    $room = $message->getRoom();
    $teamName = $room->getEquipe()->getNom();
    $roomName = $room->getName();
    $userName = $user->getFullName();

    $notification = new PlanifNotifications();
    $notification->setRecipient($message->getSender())
        ->setMessage("$userName a vu votre message dans le salon \"$roomName\"")
        ->setIsRead(false)
        ->setCreatedAt(new \DateTime());

    $this->em->persist($notification);
    $this->em->flush();
}

public function notifyNewMessage(TeamMessage $message): void
{
    $room = $message->getRoom();
    $sender = $message->getSender();
    $roomName = $room->getName();
    $teamName = $room->getEquipe()->getNom();

    foreach ($room->getMembers() as $member) {
        if ($member->getUser() !== $sender && $member->isActive()) {
            $notification = new PlanifNotifications();
            $notification->setRecipient($member->getUser())
                ->setMessage("Nouveau message de {$sender->getFullName()} dans \"$roomName\"")
                ->setIsRead(false)
                ->setCreatedAt(new \DateTime())
                ->setPlannification(null);

            $this->em->persist($notification);
        }
    }
    $this->em->flush();
}
}