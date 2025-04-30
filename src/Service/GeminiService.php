<?php

// src/Service/GeminiService.php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiService
{
    private $em;
    private $httpClient;
    private $apiKey;

    public function __construct(EntityManagerInterface $em, HttpClientInterface $httpClient, string $geminiApiKey)
    {
        $this->em = $em;
        $this->httpClient = $httpClient;
        $this->apiKey = $geminiApiKey;
    }

    private function getTableData(string $entityClass): string
    {
        $repo = $this->em->getRepository($entityClass);
        $data = $repo->createQueryBuilder('t')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $output = [];
        foreach ($data as $item) {
            $output[] = (string) $item;
        }

        return "Entity: $entityClass\nData:\n" . implode("\n", $output);
    }

    private function getPlannedTasksWithUsers(): string
    {
        $query = $this->em->createQuery('
        SELECT t, a, c FROM App\Entity\Tache t
        LEFT JOIN t.artisan a
        LEFT JOIN t.constructeur c
        WHERE t.etat = :status
    ')->setParameter('status', 'Planifié');

        $tasks = $query->getResult();
        $output = ["Planned Tasks with Assignees:"];

        foreach($tasks as $task) {
            $output[] = sprintf(
                "Task: %s | Artisan: %s | Constructeur: %s | Status: %s",
                $task->getDescription(),
                $task->getArtisan() ? (string)$task->getArtisan() : 'N/A',
                $task->getConstructeur() ? (string)$task->getConstructeur() : 'N/A',
                $task->getEtat() ?? 'N/A'
            );
        }

        return implode("\n", $output);
    }

    public function getAnswer(string $question): string
    {
        try {
            // Fetch data from multiple entities
            $dataTache = $this->getTableData('App\Entity\Tache');
            $dataPlanning = $this->getTableData('App\Entity\Plannification');
            $dataArtisan = $this->getTableData('App\Entity\Artisan');
            $dataConstructeur = $this->getTableData('App\Entity\Constructeur');
            $dataUtilisateur = $this->getTableData('App\Entity\Utilisateur');
            $plannedTasks = $this->getPlannedTasksWithUsers();

            // Construct prompt
            $prompt = "Database entities with data:\n"
                . "$dataTache\n\n"
                . "$dataPlanning\n\n"
                . "$dataArtisan\n\n"
                . "$dataConstructeur\n\n"
                . "$dataUtilisateur\n\n"
                . "Planned tasks with users:\n$plannedTasks\n\n"
                . "Answer this question based on above data: $question";

            // Send request to Gemini API
            $response = $this->httpClient->request('POST',
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$this->apiKey}", [
                    'json' => [
                        'contents' => [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

            $content = $response->toArray();
            return $content['candidates'][0]['content']['parts'][0]['text'] ?? 'No answer found';

        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
