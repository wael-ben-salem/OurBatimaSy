<?php

// src/Controller/UserController.php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RecommendationService;
class UserController extends AbstractController
{
    #[Route('/users', name: 'app_users', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        return $this->json($users, 200, [], ['groups' => ['user:list']]);
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