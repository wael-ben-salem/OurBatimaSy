<?php

namespace App\Controller\Front;

use App\Entity\Projet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front/projet')]
final class ProjetController extends AbstractController
{
    #[Route(name: 'front_projet_index', methods: ['GET'])]
public function index(EntityManagerInterface $entityManager): Response
{
    $projets = $entityManager
        ->getRepository(Projet::class)
        ->findAll();
    
    // Debug output
    dump($projets); // Check your Symfony profiler toolbar
    
    return $this->render('front/projet/index.html.twig', [
        'projets' => $projets,
    ]);
}

    #[Route('/{idProjet}', name: 'front_projet_show', methods: ['GET'])]
    public function show(Projet $projet): Response
    {
        return $this->render('front/projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }
}