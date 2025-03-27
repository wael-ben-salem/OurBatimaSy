<?php

// src/Repository/PlanningRepository.php

namespace App\Repository;

use App\Entity\Planning;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PlanningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planning::class);
    }

    public function findAllWithNotes()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.note', 'n')
            ->addSelect('n')
            ->getQuery()
            ->getResult();
    }

    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('p')
            ->join('p.note', 'n')
            ->where('n.createdBy = :user OR n.assignedTo = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    // src/Repository/PlanningRepository.php
    public function findUsersByCompletedTasks(): array
    {
        return $this->createQueryBuilder('p')
            ->select('u.id as user_id, COUNT(p.id) as completed')
            ->join('p.note', 'n')
            ->join('n.assignedTo', 'u')
            ->where('p.statut = :status')
            ->setParameter('status', 'terminé')
            ->groupBy('u.id')
            ->orderBy('completed', 'DESC')
            ->getQuery()
            ->getResult();
    }

}