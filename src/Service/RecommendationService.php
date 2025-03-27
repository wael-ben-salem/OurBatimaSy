<?php

// src/Service/RecommendationService.php
namespace App\Service;

use App\Entity\User;
use App\Repository\PlanningRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecommendationService
{
    public function __construct(
        private PlanningRepository $planningRepository,
        private EntityManagerInterface $em
    ) {}

    public function getBestPerformingUser(): ?User
    {
        $users = $this->planningRepository->findUsersByCompletedTasks();

        if (empty($users)) {
            return null;
        }

        // Simple k-NN inspired approach (k=1)
        $bestUser = null;
        $maxCompleted = 0;

        foreach ($users as $userData) {
            if ($userData['completed'] > $maxCompleted) {
                $maxCompleted = $userData['completed'];
                $bestUser = $this->em->getRepository(User::class)
                    ->find($userData['user_id']);
            }
        }

        return $bestUser;
    }
}