<?php

namespace App\Repository;

use App\Entity\Etapeprojet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etapeprojet>
 */
class EtapeprojetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etapeprojet::class);
    }

     // Add a new EtapeProjet
     public function add(Etapeprojet $etapeProjet, bool $flush = true): void
     {
         $this->getEntityManager()->persist($etapeProjet);
         if ($flush) {
             $this->getEntityManager()->flush();
         }
     }
 
     // Get all EtapeProjets
     public function findAllEtapeProjets(): array
     {
         return $this->createQueryBuilder('e')
             ->orderBy('e.id', 'ASC')
             ->getQuery()
             ->getResult();
     }
 
     // Get an EtapeProjet by ID
     public function findById(int $id): ?Etapeprojet
     {
         return $this->find($id);
     }
 
     // Get an EtapeProjet by Name
     public function findByName(string $nomEtape): ?Etapeprojet
     {
         return $this->createQueryBuilder('e')
             ->andWhere('e.nomEtape = :nom')
             ->setParameter('nom', $nomEtape)
             ->getQuery()
             ->getOneOrNullResult();
     }
 
     // Update an EtapeProjet
     public function update(Etapeprojet $etapeProjet, bool $flush = true): void
     {
         $this->getEntityManager()->persist($etapeProjet);
         if ($flush) {
             $this->getEntityManager()->flush();
         }
     }
 
     // Delete an EtapeProjet
     public function remove(Etapeprojet $etapeProjet, bool $flush = true): void
     {
         $this->getEntityManager()->remove($etapeProjet);
         if ($flush) {
             $this->getEntityManager()->flush();
         }
     }

    //    /**
    //     * @return Etapeprojet[] Returns an array of Etapeprojet objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Etapeprojet
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
