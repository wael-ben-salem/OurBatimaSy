<?php
// src/Controller/EquipeController.php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\TeamRoom;
use App\Entity\TeamMember;

use App\Entity\Constructeur;
use App\Entity\Gestionnairestock;
use App\Service\TeamNotificationService;

use App\Entity\Artisan;
use App\Entity\Projet;
use Psr\Log\LoggerInterface;

use App\Entity\Client;
use App\Entity\Etapeprojet;


use App\Repository\ProjetRepository;

use Doctrine\ORM\Exception\ORMException;

use Symfony\Component\Security\Core\Security;

use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use App\Repository\TacheRepository;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;

#[Route('/equipe')]
class EquipeController extends AbstractController
{
    #[Route('/', name: 'app_equipe_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository): Response
    {
        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipeRepository->findAllWithDetails(),
            'totalPages' => ceil(count($equipeRepository->findAll()) / 4)
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

    // Récupérer le client associé via la relation inverse
    $client = $entityManager->getRepository(Client::class)->findOneBy(['client' => $user]);
    
    if (!$client) {
        throw $this->createNotFoundException('Profil client non trouvé');
    }

    // Récupérer les projets du client avec les équipes jointes
    $projets = $entityManager->getRepository(Projet::class)->createQueryBuilder('p')
        ->leftJoin('p.idEquipe', 'e')
        ->addSelect('e')
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
        'user' => $user
    ]);
}#[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
public function new(
    Request $request, 
    EntityManagerInterface $em, 
    TeamNotificationService $notificationService,
    LoggerInterface $logger
): Response {
    // Récupération des utilisateurs pour le formulaire GET
    $constructeurs = $em->getRepository(Constructeur::class)->findAll();
    $gestionnaires = $em->getRepository(Gestionnairestock::class)->findAll();
    $artisans = $em->getRepository(Artisan::class)->findAll();

    // Traitement du formulaire POST
    if ($request->isMethod('POST')) {
        $data = json_decode($request->getContent(), true);
        
        // Validation des données requises
        if (!isset($data['nom'], $data['constructeur'], $data['gestionnaire'])) {
            return $this->json(['error' => 'Données manquantes'], Response::HTTP_BAD_REQUEST);
        }

        // Début de la transaction
        $em->beginTransaction();

        try {
            // Création de l'équipe
            $equipe = new Equipe();
            $equipe->setNom($data['nom']);
            $equipe->setDateCreation(new \DateTimeImmutable());
            $equipe->setRating($data['rating'] ?? 0);

            // Assignation du constructeur
            $constructeur = $em->find(Constructeur::class, $data['constructeur']);
            if (!$constructeur) {
                throw new \Exception('Constructeur non trouvé');
            }
            $equipe->setConstructeur($constructeur);

            // Assignation du gestionnaire
            $gestionnaire = $em->find(Gestionnairestock::class, $data['gestionnaire']);
            if (!$gestionnaire) {
                throw new \Exception('Gestionnaire non trouvé');
            }
            $equipe->setGestionnairestock($gestionnaire);

            // Ajout des artisans
            foreach ($data['artisans'] ?? [] as $artisanId) {
                $artisan = $em->find(Artisan::class, $artisanId);
                if ($artisan) {
                    $equipe->addArtisan($artisan);
                }
            }

            // Persistance de l'équipe
            $em->persist($equipe);

            // Création du salon principal
            $room = new TeamRoom();
            $room->setEquipe($equipe)
                ->setName('Discussion Générale - ' . $equipe->getNom());
            $em->persist($room);

            // Premier flush pour générer les IDs
            $em->flush();

            // Création des TeamMember pour tous les utilisateurs de l'équipe
            $usersInTeam = [];
            
            // Ajout de l'utilisateur du constructeur
            if ($constructeur->getConstructeur()) {
                $usersInTeam[] = $constructeur->getConstructeur();
            }
            
            // Ajout de l'utilisateur du gestionnaire
            if ($gestionnaire->getGestionnairestock()) {
                $usersInTeam[] = $gestionnaire->getGestionnairestock();
            }
            
            // Ajout des utilisateurs des artisans
            foreach ($equipe->getArtisans() as $artisan) {
                if ($artisan->getArtisan()) {
                    $usersInTeam[] = $artisan->getArtisan();
                }
            }

            // Création des TeamMember pour chaque utilisateur
            foreach ($usersInTeam as $user) {
                $teamMember = new TeamMember();
                $teamMember->setRoom($room)
                    ->setUser($user)
                    ->setJoinedAt(new \DateTime())
                    ->setIsActive(true);
                $em->persist($teamMember);
            }

            // Envoi des notifications
            $notificationService->sendTeamCreationNotifications($equipe);
            $notificationService->sendRoomInvitation($equipe, $room);

            // Validation de la transaction
            $em->commit();

            return $this->json([
                'success' => true,
                'id' => $equipe->getId(),
                'message' => 'Équipe créée avec succès'
            ]);

        } catch (\Exception $e) {
            // Rollback en cas d'erreur
            $em->rollback();
            $logger->error('Échec de création d\'équipe', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->json([
                'error' => 'Erreur lors de la création',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Rendu du formulaire pour les requêtes GET
    return $this->render('equipe/new.html.twig', [
        'constructeurs' => $constructeurs,
        'gestionnaires' => $gestionnaires,
        'artisans' => $artisans,
    ]);
}
#[Route('/table', name: 'app_equipe_table', methods: ['GET'])]
public function getTeamTable(
    Request $request, 
    EquipeRepository $equipeRepository
): Response
{
    // Paramètres de pagination
    $page = $request->query->getInt('page', 1);
    $limit = 4;
    $searchTerm = $request->query->get('search', '');
    
    // Construction de la requête
    $queryBuilder = $equipeRepository->createQueryBuilder('e')
        ->leftJoin('e.constructeur', 'c')
        ->leftJoin('e.gestionnairestock', 'g')
        ->addSelect('c', 'g')
        ->orderBy('e.id', 'DESC');

    if ($searchTerm) {
        $queryBuilder->andWhere('e.nom LIKE :search')
            ->setParameter('search', '%'.$searchTerm.'%');
    }

    // Pagination
    $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($queryBuilder);
    $totalTeams = count($paginator);
    $totalPages = ceil($totalTeams / $limit);

    $equipes = $queryBuilder
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();

    return $this->render('equipe/_team_table.html.twig', [
        'equipes' => $equipes,
        'currentPage' => $page,
        'totalPages' => $totalPages,
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
   