<?php

namespace App\Controller;

use App\Entity\Projet; // Add this use statement
use App\Service\GeminiApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProjectEstimationController extends AbstractController
{
    #[Route('/project/estimation/{id}', name: 'app_project_estimation')]
    public function getEstimation(
        int $id, 
        Request $request, 
        GeminiApiService $geminiApi,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Get the project from database
        $project = $entityManager->getRepository(Projet::class)->find($id);
        
        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }
        
        // Get terrain details
        $terrain = $project->getIdTerrain();
        
        // Call Gemini API
        $estimation = $geminiApi->getEstimation(
            $project->getStylearch(),
            $terrain ? (string)$terrain->getSuperficie() : '',
            $terrain ? $terrain->getEmplacement() : '',
            $project->getType()
        );
        
        // Parse the response
        $estimationData = json_decode($estimation, true);
        $estimationText = $estimationData['candidates'][0]['content']['parts'][0]['text'] ?? 'No estimation available';
        
        // Clean up the text
        $cleanText = preg_replace('/\*\*/', '', $estimationText);
        
        return $this->json([
            'estimation' => $cleanText,
            'project' => [
                'name' => $project->getNomprojet()
            ]
        ]);
    }
}