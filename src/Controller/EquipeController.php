<?php
// src/Controller/EquipeController.php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Constructeur;
use App\Entity\Artisan;
use App\Entity\Projet;
use App\Entity\Client;
use App\Entity\Etapeprojet;


use App\Repository\ProjetRepository;


use Symfony\Component\Security\Core\Security;

use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use App\Repository\TacheRepository;
use App\Repository\ArticleRepository;

use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\Gestionnairestock;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/equipe')]
class EquipeController extends AbstractController
{
    #[Route('/', name: 'app_equipe_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository): Response
    {
        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipeRepository->findAllWithDetails(),
        ]);
    }
    #[Route('/mesequipes', name: 'app_equipe_client')]
    public function equipesParClient(EntityManagerInterface $entityManager, Security $security, EquipeRepository $equipeRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
        
        if (!$user || !in_array('ROLE_CLIENT', $user->getRoles())) {
            throw $this->createAccessDeniedException('Accès réservé aux clients');
        }
    
        // Récupérer le client associé
        $client = $user->getClient();
        if (!$client) {
            throw $this->createNotFoundException('Profil client non trouvé');
        }

    // Récupérer les projets du client avec les équipes jointes
    $projets = $entityManager->getRepository(Projet::class)->createQueryBuilder('p')
        ->join('p.idEquipe', 'e')
        ->where('p.idClient = :client')
        ->setParameter('client', $client)
        ->getQuery()
        ->getResult();

    // Récupérer les équipes complètes avec leurs relations
    $equipes = [];
    foreach ($projets as $projet) {
        if ($projet->getIdEquipe()) {
            $equipe = $equipeRepository->findWithDetails($projet->getIdEquipe()->getId());
            if ($equipe && !in_array($equipe, $equipes, true)) {
                $equipes[] = $equipe;
            }
        }
    }

    return $this->render('equipe/client_equipes.html.twig', [
        'equipes' => $equipes,
        'user' => $user  // On passe l'utilisateur directement
    ]);
}

    #[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $equipe = new Equipe();
        
        // Récupérer tous les utilisateurs par rôle
        $constructeurs = $em->getRepository(Constructeur::class)->findAll();
        $gestionnaires = $em->getRepository(Gestionnairestock::class)->findAll();
        $artisans = $em->getRepository(Artisan::class)->findAll();
    
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
            
            // Récupérer les IDs sélectionnés
            $constructeurId = $data['constructeur'];
            $gestionnaireId = $data['gestionnaire'];
            $artisansIds = $data['artisans'] ?? [];
    
            // Assigner à l'équipe
            $equipe->setNom($data['nom']);
            $equipe->setRating($data['rating'] ?? 0);
            $equipe->setConstructeur($em->find(Constructeur::class, $constructeurId));
            $equipe->setGestionnairestock($em->find(Gestionnairestock::class, $gestionnaireId));
            
            // Ajouter les artisans
            foreach ($artisansIds as $artisanId) {
                $artisan = $em->find(Artisan::class, $artisanId);
                $equipe->addArtisan($artisan);
            }
    
            $em->persist($equipe);
            $em->flush();
    
            return $this->json(['success' => true, 'id' => $equipe->getId()]);
        }
    
        return $this->render('equipe/new.html.twig', [
            'constructeurs' => $constructeurs,
            'gestionnaires' => $gestionnaires,
            'artisans' => $artisans,
        ]);
    }

    #[Route('/{id}', name: 'app_equipe_show', methods: ['GET'])]
public function show(
    $id,
    EquipeRepository $equipeRepository,
    TacheRepository $tacheRepository,
    ArticleRepository $articleRepository
): Response {
    $id = (int)$id;

    $equipe = $equipeRepository->findOneWithDetails($id);

    if (!$equipe) {
        throw $this->createNotFoundException('Équipe non trouvée');
    }

    // Récupérer les tâches des artisans de l'équipe
    $taches = $tacheRepository->findByArtisansInEquipe($equipe->getId());

    // Récupérer les matériels associés aux projets de l'équipe
    $materiels = $articleRepository->findByEquipe($equipe->getId());

    // Calcul des stats
    $stats = [
        'projets' => count($equipe->getProjets()),
        'taches' => count($taches),
        'materiels' => count($materiels)
    ];

    return $this->render('equipe/show.html.twig', [
        'equipe' => $equipe,
        'stats' => $stats,
        'taches' => $taches,
        'materiels' => $materiels
    ]);
}

    #[Route('/{id}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $em): Response
    {
        // Récupérer tous les utilisateurs par rôle
        $constructeurs = $em->getRepository(Constructeur::class)->findAll();
        $gestionnaires = $em->getRepository(Gestionnairestock::class)->findAll();
        $artisans = $em->getRepository(Artisan::class)->findAll();
    
        // Récupérer les IDs sélectionnés
        $selectedConstructor = $equipe->getConstructeur() ? $equipe->getConstructeur()->getId() : null;
        $selectedGestionnaire = $equipe->getGestionnairestock() ? $equipe->getGestionnairestock()->getId() : null;
        $selectedArtisans = $equipe->getArtisan()->map(fn($a) => $a->getId())->toArray();
    
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
            
            // Mettre à jour l'équipe
            $equipe->setNom($data['nom']);
            $equipe->setRating($data['rating'] ?? 0);
            
            // Mettre à jour les membres
            $equipe->setConstructeur($em->find(Constructeur::class, $data['constructeur']));
            $equipe->setGestionnairestock($em->find(Gestionnairestock::class, $data['gestionnaire']));
            
            // Mettre à jour les artisans
            $equipe->getArtisan()->clear();
            foreach ($data['artisans'] as $artisanId) {
                $artisan = $em->find(Artisan::class, $artisanId);
                $equipe->addArtisan($artisan);
            }
    
            $em->flush();
    
            return $this->json(['success' => true, 'id' => $equipe->getId()]);
        }
    
        return $this->render('equipe/edit.html.twig', [
            'equipe' => $equipe,
            'constructeurs' => $constructeurs,
            'gestionnaires' => $gestionnaires,
            'artisans' => $artisans,
            'selectedConstructor' => $selectedConstructor,
            'selectedGestionnaire' => $selectedGestionnaire,
            'selectedArtisans' => $selectedArtisans,
        ]);
    }

    #[Route('/{id}', name: 'app_equipe_delete', methods: ['POST'])]
    public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipe_index');
    }
    #[Route('/client/{id}/show', name: 'app_client_projets_show', methods: ['GET'])]
public function showClient(
    $id,
    EntityManagerInterface $em,
    EquipeRepository $equipeRepository,
    TacheRepository $tacheRepository,
    ArticleRepository $articleRepository,
    ProjetRepository $projetRepository,
    Security $security
): Response {
    // Récupérer l'utilisateur connecté
    $user = $security->getUser();
    
    if (!$user || !in_array('ROLE_CLIENT', $user->getRoles())) {
        throw $this->createAccessDeniedException('Accès réservé aux clients');
    }

    // Récupérer le client via l'utilisateur
    $client = $em->getRepository(Client::class)->findOneBy(['client' => $user]);
    if (!$client) {
        throw $this->createNotFoundException('Profil client non trouvé');
    }

    // Vérifier que l'ID correspond à l'utilisateur
    if ($user->getId() != $id) {
        throw $this->createAccessDeniedException('Accès non autorisé');
    }

    // Récupérer les projets du client avec les équipes jointes
    $projets = $projetRepository->createQueryBuilder('p')
        ->leftJoin('p.idEquipe', 'e')
        ->addSelect('e')
        ->where('p.idClient = :client')
        ->setParameter('client', $client)
        ->getQuery()
        ->getResult();

    // Récupérer toutes les équipes associées aux projets du client
    $equipesIds = array_map(fn($p) => $p->getIdEquipe()?->getId(), $projets);
    $equipesIds = array_unique(array_filter($equipesIds));

    // Si aucune équipe n'est associée, on peut retourner une vue vide
    if (empty($equipesIds)) {
        return $this->render('equipe/client_equipes_show.html.twig', [
            'client' => $client,
            'user' => $user,
            'projets' => [],
            'equipe' => null,
            'stats' => [
                'total' => 0,
                'budget_total' => 0,
                'actifs' => 0,
                'termines' => 0,
                'en_attente' => 0
            ],
            'chartData' => [
                'status' => [],
                'types' => []
            ],
            'taches' => [],
            'materiels' => []
        ]);
    }

    // Pour cet exemple, on prend la première équipe (vous pouvez adapter selon vos besoins)
    $equipe = $equipeRepository->findOneWithDetails($equipesIds[0]);

    // Récupérer les tâches des artisans de l'équipe
    $taches = $tacheRepository->findByArtisansInEquipe($equipe->getId());
    $total_taches = count($taches);
    $taches_terminees = count(array_filter($taches, fn($t) => $t->getEtat() === 'Terminé'));
    $taches_en_cours = count(array_filter($taches, fn($t) => $t->getEtat() === 'En cours'));

    // Récupérer les matériels associés aux projets de l'équipe
    $materiels = $articleRepository->findByEquipe($equipe->getId());

    // Calcul des statistiques des projets
    $stats = [
        'total' => count($projets),
        'budget_total' => array_reduce($projets, fn($sum, $p) => $sum + $p->getBudget(), 0),
        'actifs' => count(array_filter($projets, fn($p) => $p->getEtat() === 'En cours')),
        'termines' => count(array_filter($projets, fn($p) => $p->getEtat() === 'Terminé')),
        'en_attente' => count(array_filter($projets, fn($p) => $p->getEtat() === 'En attente'))
    ];

    // Récupérer les tâches récemment terminées (5 dernières)
    $recent_completed_tasks = array_slice(
        array_filter($taches, fn($t) => $t->getEtat() === 'Terminé'),
        -5
    );

    // Préparation des données pour les graphiques
    $chartData = [
        'status' => [
            'En cours' => $stats['actifs'],
            'Terminé' => $stats['termines'],
            'En attente' => $stats['en_attente']
        ],
        'types' => array_reduce($projets, function($carry, $p) {
            $type = $p->getType() ?? 'Non spécifié';
            $carry[$type] = ($carry[$type] ?? 0) + 1;
            return $carry;
        }, [])
    ];

    return $this->render('equipe/client_equipes_show.html.twig', [
        'client' => $client,
        'user' => $user,
        'projets' => $projets,
        'stats' => $stats,
        'chartData' => $chartData,
        'equipe' => $equipe,
        'taches' => $taches,
        'materiels' => $materiels,
        'budget_total' => array_reduce($projets, fn($sum, $p) => $sum + $p->getBudget(), 0),
        'total_taches' => $total_taches,
        'taches_terminees' => $taches_terminees,
        'taches_en_cours' => $taches_en_cours,
        'recent_completed_tasks' => $recent_completed_tasks
    ]);
}
}
   