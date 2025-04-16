<?php

namespace App\Repository;

use App\Entity\Projet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Projet>
 */
class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projet::class);
    }

    public function findProjetById(int $id): ?Projet
    {
    return $this->createQueryBuilder('p')
        ->andWhere('p.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function findAllProjets(): array
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
    }
    
    public function findAllWithStages()
{
    return $this->createQueryBuilder('p')
        ->leftJoin('p.etapeprojets', 'e')
        ->addSelect('e')
        ->orderBy('p.datecreation', 'DESC')
        ->getQuery()
        ->getResult();
}

    public function findEtapesForProjet(int $projetId): array
    {
        return $this->createQueryBuilder('p')
            ->select('e.nomEtape')
            ->join('p.etapes', 'e')
            ->where('p.id = :projetId')
            ->setParameter('projetId', $projetId)
            ->getQuery()
            ->getResult();
    }

    public function findTerrainForProjet(int $projetId)
    {
        return $this->createQueryBuilder('p')
            ->select('t.emplacement')
            ->join('p.terrain', 't')
            ->where('p.id = :projetId')
            ->setParameter('projetId', $projetId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function updateProjet(Projet $projet)
    {
        $this->_em->persist($projet);
        $this->_em->flush();
    }

    public function deleteProjet(int $id)
    {
        $projet = $this->find($id);
        if ($projet) {
            $this->_em->remove($projet);
            $this->_em->flush();
        }
    }

    public function assignEquipeToProjet(int $projetId, int $equipeId)
    {
        $projet = $this->find($projetId);
        if ($projet) {
            $projet->setEquipeId($equipeId);
            $this->_em->flush();
        }
    }

    public function getIdByNom(string $nomProjet): ?int
    {
        return $this->createQueryBuilder('p')
            ->select('p.id')
            ->where('p.nomProjet = :nomProjet')
            ->setParameter('nomProjet', $nomProjet)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAllProjetNames(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.nomProjet')
            ->getQuery()
            ->getResult();
    }

    public function getProjectTypes(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.type')
            ->getQuery()
            ->getResult();
    }

    public function getLastInsertedProjet(): ?Projet
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    //    /**
    //     * @return Projet[] Returns an array of Projet objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Projet
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
