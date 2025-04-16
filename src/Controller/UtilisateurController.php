<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Repository\ArtisanRepository;
use App\Repository\ConstructeurRepository;
use App\Service\RecommendationService;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    #[Route('/tables', name: 'app_tables')]
    public function tables(): Response
    {
        return $this->render('tables/index.html.twig');
    }

    #[Route('/tables/role/{role}', name: 'app_users_by_role', methods: ['GET'])]
    public function getUsersByRole(
        string $role,
        UtilisateurRepository $userRepo,
        ArtisanRepository $artisanRepo,
        ConstructeurRepository $constructeurRepo
    ): Response {
        $users = $userRepo->findBy([
            'role' => $role,
            'isconfirmed' => true
        ]);

        return $this->render('tables/_user_table.html.twig', [
            'users' => $users,
            'role' => $role,
            'artisanInfos' => $role === 'Artisan' ? $artisanRepo->findAll() : [],
            'constructeurInfos' => $role === 'Constructeur' ? $constructeurRepo->findAll() : [],
        ]);
    }

    #[Route('/users', name: 'app_users', methods: ['GET'])]
    public function index(UtilisateurRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        return $this->json($users, 200, [], ['groups' => ['user:list']]);
    }

    #[Route('/utilisateur/{id}', name: 'utilisateur_show')]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('tables/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/utilisateur/{id}/delete', name: 'utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $utilisateur->getId(), $request->request->get('_token'))) {
            $em->remove($utilisateur);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }

        return $this->redirectToRoute('app_tables');
    }

    #[Route('/users/recommended', name: 'app_recommended_user', methods: ['GET'])]
    public function recommendedUser(RecommendationService $recommendationService): JsonResponse
    {
        $user = $recommendationService->getBestPerformingUser();

        if (!$user) {
            return $this->json(['error' => 'No users found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'completedPlannings' => $user->getCompletedPlannings()
        ]);
    }
}
