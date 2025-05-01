<?php
// src/Repository/TeamRoomRepository.php
namespace App\Repository;

use App\Entity\Utilisateur;
use App\Entity\TeamRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TeamRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamRoom::class);
    }

    public function findRoomsForUser(Utilisateur $user, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->join('r.members', 'm')
            ->where('m.user = :user')
            ->setParameter('user', $user)
            ->orderBy('r.lastActivity', 'DESC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
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