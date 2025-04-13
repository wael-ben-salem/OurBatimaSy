<?php

namespace App\Repository;

use App\Entity\Terrain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Terrain>
 */
class TerrainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Terrain::class);
    }

        /**
     * Add a new Terrain entity.
     */
    public function addTerrain(Terrain $terrain, bool $flush = true): void
    {
        $this->getEntityManager()->persist($terrain);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retrieve all terrains.
     */
    public function getAllTerrain(): array
    {
        return $this->createQueryBuilder('t')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retrieve all emplacements.
     */
    public function getAllEmplacements(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.emplacement')
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Retrieve a Terrain by ID.
     */
    public function getTerrainById(int $id): ?Terrain
    {
        return $this->find($id);
    }

    /**
     * Retrieve a Terrain by emplacement.
     */
    public function getTerrainByEmplacement(string $emplacement): ?Terrain
    {
        return $this->findOneBy(['emplacement' => $emplacement]);
    }

    /**
     * Update an existing Terrain entity.
     */
    public function updateTerrain(Terrain $terrain, bool $flush = true): void
    {
        $this->getEntityManager()->persist($terrain);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Delete a Terrain by ID.
     */
    public function deleteTerrain(int $id): void
    {
        $terrain = $this->find($id);
        if ($terrain) {
            $this->getEntityManager()->remove($terrain);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get the latest inserted Terrain.
     */
    public function getLastInsertedTerrain(): ?Terrain
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }




    //    /**
    //     * @return Terrain[] Returns an array of Terrain objects
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

    //    public function findOneBySomeField($value): ?Terrain
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
