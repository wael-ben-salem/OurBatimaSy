<?php
// src/Repository/EquipeRepository.php

namespace App\Repository;

use App\Entity\Equipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipe::class);
    }

    public function findAllWithDetails()
{
    return $this->createQueryBuilder('e')
        ->addSelect('c', 'cc', 'g', 'gg', 'a', 'aa')
        ->leftJoin('e.constructeur', 'c')
        ->leftJoin('c.constructeur', 'cc') // Relation Constructeur -> User
        ->leftJoin('e.gestionnairestock', 'g')
        ->leftJoin('g.gestionnairestock', 'gg') // Relation GestionnaireStock -> User
        ->leftJoin('e.artisan', 'a')
        ->leftJoin('a.artisan', 'aa') // Relation Artisan -> User
        ->getQuery()
        ->getResult();
}

public function findOneWithDetails(int $id): ?Equipe
{
    return $this->createQueryBuilder('e')
        ->addSelect('c', 'cc', 'g', 'gg', 'a', 'aa', 'p')
        ->leftJoin('e.constructeur', 'c')
        ->leftJoin('c.constructeur', 'cc')
        ->leftJoin('e.gestionnairestock', 'g')
        ->leftJoin('g.gestionnairestock', 'gg')
        ->leftJoin('e.artisan', 'a')
        ->leftJoin('a.artisan', 'aa')
        ->leftJoin('e.projets', 'p') // Relation OneToMany avec Projet
        ->where('e.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();
}
public function findWithDetails(int $id): ?Equipe
{
    return $this->createQueryBuilder('e')
        ->leftJoin('e.projets', 'p')
        ->leftJoin('e.constructeur', 'c')
        ->leftJoin('e.gestionnairestock', 'g')
        ->leftJoin('e.artisan', 'a')
        ->addSelect('p', 'c', 'g', 'a')
        ->where('e.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();
}
public function findAllPaginated(int $page = 1, int $limit = 4): array
{
    $query = $this->createQueryBuilder('e')
        ->leftJoin('e.constructeur', 'c')
        ->leftJoin('e.gestionnairestock', 'g')
        ->leftJoin('e.artisan', 'a')
        ->addSelect('c', 'g', 'a')
        ->orderBy('e.id', 'DESC')
        ->getQuery();

    $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
    $paginator->getQuery()
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit);

    return iterator_to_array($paginator);
}
}