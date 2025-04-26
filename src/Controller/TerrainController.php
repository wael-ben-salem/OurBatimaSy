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
use App\Service\MapService;

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
            // Get coordinates from form
            $latitude = $form->get('latitude')->getData();
            $longitude = $form->get('longitude')->getData();
            
            // Set coordinates on terrain
            $terrain->setLatitude($latitude);
            $terrain->setLongitude($longitude);
            
            // Only perform reverse geocoding if emplacement is empty
            if (empty($terrain->getEmplacement())) {
                $address = $this->reverseGeocode($latitude, $longitude);
                if ($address) {
                    $terrain->setEmplacement($address);
                }
            }
            
            // Set detailsgeo if empty
            if (empty($terrain->getDetailsgeo())) {
                $terrain->setDetailsgeo("Lat: $latitude, Lng: $longitude");
            }
    
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

private function reverseGeocode(float $latitude, float $longitude): ?string
{
    $apiKey = '67bf5aececfa5982522390euj6000e5';
    $url = "https://geocode.maps.co/reverse?lat=$latitude&lon=$longitude&api_key=$apiKey";
    
    try {
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        
        return $data['display_name'] ?? null;
    } catch (\Exception $e) {
        // Log error if needed
        return null;
    }
}

#[Route('/{idTerrain}', name: 'app_terrain_show', methods: ['GET'])]
public function show(Terrain $terrain, MapService $mapService): Response
{
    // Extract coordinates from detailsgeo or use defaults
    $coordinates = $this->extractCoordinates($terrain->getDetailsgeo());
    
    // Generate map HTML
    $mapHTML = $mapService->generateMapHTML(
        $coordinates['lat'],
        $coordinates['lng'],
        $terrain->getEmplacement(),
        $terrain->getCaracteristiques()
    );

    return $this->render('terrain/show.html.twig', [
        'terrain' => $terrain,
        'mapHTML' => $mapHTML
    ]);
}

private function extractCoordinates(string $detailsgeo): array
{
    $default = ['lat' => 36.8, 'lng' => 10.18]; // Default Tunisia coordinates
    
    if (preg_match('/Lat: ([\d.-]+), Lng: ([\d.-]+)/', $detailsgeo, $matches)) {
        return [
            'lat' => (float)$matches[1],
            'lng' => (float)$matches[2]
        ];
    }
    
    return $default;
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
            $latitude = $form->get('latitude')->getData();
            $longitude = $form->get('longitude')->getData();
            
            $terrain->setLatitude($latitude);
            $terrain->setLongitude($longitude);
            
            if (empty($terrain->getEmplacement())) {
                $address = $this->reverseGeocode($latitude, $longitude);
                if ($address) {
                    $terrain->setEmplacement($address);
                }
            }
            
            if (empty($terrain->getDetailsgeo())) {
                $terrain->setDetailsgeo("Lat: $latitude, Lng: $longitude");
            }
    
            $entityManager->persist($terrain);
            $entityManager->flush();
            
            $this->addFlash('success', 'Terrain ajouté avec succès. Vous pouvez maintenant créer votre projet.');
            return $this->redirectToRoute('app_projet_front_new');
        }
    
        return $this->render('terrainFront/new.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/front/terrain/{idTerrain}', name: 'app_terrain_front_show', methods: ['GET'])]
    public function frontShow(Terrain $terrain): Response
    {
        return $this->render('terrainFront/show.html.twig', [
            'terrain' => $terrain,
        ]);
    }

}
