<?php

// src/Repository/TeamMessageRepository.php

namespace App\Repository;

use App\Entity\TeamMessage;
use App\Entity\TeamRoom;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TeamMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamMessage::class);
    }

    /**
     * @return TeamMessage[]
     */
    public function findUnreadMessagesForUser(TeamRoom $room, Utilisateur $user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.room = :room')
            ->andWhere('m.sender != :user')
            ->andWhere('m.isRead = false')
            ->setParameter('room', $room)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function countUnreadMessagesForUser(TeamRoom $room, Utilisateur $user): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.room = :room')
            ->andWhere('m.sender != :user')
            ->andWhere('m.isRead = false')
            ->setParameter('room', $room)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countTotalUnreadForUser(Utilisateur $user): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->join('m.room', 'r')
            ->join('r.members', 'rm')
            ->where('rm.user = :user')
            ->andWhere('m.sender != :user')
            ->andWhere('m.isRead = false')
            ->andWhere('rm.isActive = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

public function countUnreadMessages(TeamRoom $room, Utilisateur $user): int
{
    return (int) $this->createQueryBuilder('m')
        ->select('COUNT(m.id)')
        ->where('m.room = :room')
        ->andWhere('m.sender != :user')
        ->andWhere('m.isRead = false')
        ->setParameter('room', $room)
        ->setParameter('user', $user)
        ->getQuery()
        ->getSingleScalarResult();
}
    public function markMessagesAsRead(TeamRoom $room, Utilisateur $user): void
    {
        $this->createQueryBuilder('m')
            ->update()
            ->set('m.isRead', true)
            ->where('m.room = :room')
            ->andWhere('m.sender != :user')
            ->andWhere('m.isRead = false')
            ->setParameter('room', $room)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}