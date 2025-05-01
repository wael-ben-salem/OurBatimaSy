<?php

namespace App\Repository;

use App\Entity\Tache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tache>
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    //    /**
    //     * @return Tache[] Returns an array of Tache objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tache
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findByArtisansInEquipe(int $equipeId): array
{
    return $this->createQueryBuilder('t')
        ->join('t.artisan', 'a')
        ->join('a.equipe', 'e')
        ->where('e.id = :equipeId')
        ->setParameter('equipeId', $equipeId)
        ->getQuery()
        ->getResult();
}

// In TacheRepository.php
    public function findAll(): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.constructeur', 'c')
            ->addSelect('c')
            ->leftJoin('t.artisan', 'a')
            ->addSelect('a')
            ->getQuery()
            ->getResult();
    }
}
