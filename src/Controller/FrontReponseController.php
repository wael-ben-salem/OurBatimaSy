<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontReponseController extends AbstractController {
    #[Route('/front/reponse', name: 'app_front_reponse')]
    public function index(): Response
    {
        return $this->render('front_reponse/index.html.twig', [
            'controller_name' => 'FrontReponseController',
        ]);
    }

    #[Route('/Reclamation', name: 'front_reponses_index')]
    public function listResponses(EntityManagerInterface $entityManager): Response
    {
        // Use a custom query to fetch responses
        $conn = $entityManager->getConnection();
        $sql = 'SELECT * FROM reponse';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $responses = $resultSet->fetchAllAssociative();

        return $this->render('front_reponse/index.html.twig', [
            'responses' => $responses,
        ]);
    }

    #[Route('/Reclamation/{id}', name: 'front_reponse_show', methods: ['GET'])]
    public function showResponse(EntityManagerInterface $entityManager, int $id): Response
    {
        // Use a custom query to fetch a single response
        $conn = $entityManager->getConnection();
        $sql = 'SELECT * FROM reponse WHERE id = :id';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $id]);
        $response = $resultSet->fetchAssociative();

        if (!$response) {
            throw $this->createNotFoundException('Response not found');
        }

        return $this->render('front_reponse/show.html.twig', [
            'response' => $response,
        ]);
    }
}

