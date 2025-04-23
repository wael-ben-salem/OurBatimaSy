<?php

namespace App\Controller;

use App\Entity\Terrain;
use App\Form\TerrainType;
use App\Entity\Projet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/terrain')]
final class TerrainController extends AbstractController
{
    #[Route(name: 'app_terrain_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $terrains = $entityManager
            ->getRepository(Terrain::class)
            ->findAll();

        return $this->render('terrain/index.html.twig', [
            'terrains' => $terrains,
        ]);
    }

    #[Route('/new', name: 'app_terrain_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $terrain = new Terrain();
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($terrain);
            $entityManager->flush();
            
            $this->addFlash('success', 'Terrain ajouté avec succès. Sélectionnez-le dans le champ "emplacement".');
            return $this->redirectToRoute('app_projet_new');
        }

        return $this->render('terrain/new.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/{idTerrain}', name: 'app_terrain_show', methods: ['GET'])]
    public function show(Terrain $terrain): Response
    {
        return $this->render('terrain/show.html.twig', [
            'terrain' => $terrain,
        ]);
    }

    #[Route('/{idTerrain}/edit', name: 'app_terrain_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Terrain modifié avec succès.');
                return $this->redirectToRoute('app_terrain_show', ['idTerrain' => $terrain->getIdTerrain()]);
            } else {
                $this->addFlash('error', 'Veuillez vérifier à nouveau les champs.');
            }
        }

        return $this->render('terrain/edit.html.twig', [
            'terrain' => $terrain,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{idTerrain}', name: 'app_terrain_delete', methods: ['POST'])]
    public function delete(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$terrain->getIdTerrain(), $request->getPayload()->getString('_token'))) {
            $projetRepository = $entityManager->getRepository(Projet::class);
            $relatedProjects = $projetRepository->findBy(['idTerrain' => $terrain]);
            
            if (count($relatedProjects) > 0) {
                $projectCount = count($relatedProjects);
                $projectWord = $projectCount > 1 ? 'projets' : 'projet';
                
                $this->addFlash('warning', sprintf(
                    'Suppression impossible : Ce terrain est utilisé par un projet. '.
                    'Veuillez d\'abord supprimer ou modifier le projet concernés.',
                ));
                return $this->redirectToRoute('app_terrain_show', ['idTerrain' => $terrain->getIdTerrain()]);
            }
            
            try {
                $entityManager->remove($terrain);
                $entityManager->flush();
                $this->addFlash('success', 'Le terrain a été supprimé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur technique est survenue lors de la suppression. Veuillez réessayer.');
            }
        }
    
        return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
    }


    //front office
    #[Route('/front/newterrain', name: 'app_terrain_front_new', methods: ['GET', 'POST'])]
    public function frontNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $terrain = new Terrain();
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($terrain);
            $entityManager->flush();
            
            $this->addFlash('success', 'Terrain ajouté avec succès. Sélectionnez-le dans le champ "emplacement".');
            return $this->redirectToRoute('app_terrain_front_new');
        }

        return $this->render('terrainFront/new.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }



}
