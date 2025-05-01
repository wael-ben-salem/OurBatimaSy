<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

#[Route('/front/reponse')]
class FrontReponseController extends AbstractController
{
    #[Route('/', name: 'front_reponse_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Use a custom query to fetch all responses with their associated reclamations
        $conn = $entityManager->getConnection();
        $sql = 'SELECT r.*, rec.description as reclamation_description, rec.id as reclamation_id
                FROM reponse r
                LEFT JOIN reclamation rec ON r.id_Reclamation = rec.id
                ORDER BY r.date DESC';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $reponses = $resultSet->fetchAllAssociative();

        return $this->render('front_reponse/index.html.twig', [
            'reponses' => $reponses,
        ]);
    }

    #[Route('/{id}', name: 'front_reponse_show', methods: ['GET'])]
    public function show(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator, int $id): Response
    {
        // Use a custom query to fetch a single response with its associated reclamation
        $conn = $entityManager->getConnection();
        $sql = 'SELECT r.*, rec.description as reclamation_description, rec.id as reclamation_id
                FROM reponse r
                LEFT JOIN reclamation rec ON r.id_Reclamation = rec.id
                WHERE r.id = :id';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $id]);
        $reponse = $resultSet->fetchAssociative();

        if (!$reponse) {
            throw $this->createNotFoundException('Réponse non trouvée');
        }

        // Get the requested target language from the query parameter, default to original (no translation)
        $targetLang = $request->query->get('lang', 'original');

        // Store the original description
        $originalDescription = $reponse['description'];
        $translatedDescription = null;

        // If a translation is requested
        if ($targetLang !== 'original' && in_array($targetLang, ['en', 'ar'])) {
            // Here we would normally use a translation API like Google Translate, DeepL, etc.
            // For demonstration purposes, we'll use a simple mapping
            $translations = [
                'en' => [
                    // Sample translations for demonstration
                    'Ceci est une réponse.' => 'This is a response.',
                    'Nous avons bien reçu votre réclamation.' => 'We have received your claim.',
                    'Votre problème sera résolu sous peu.' => 'Your issue will be resolved shortly.',
                    'Merci pour votre patience.' => 'Thank you for your patience.',
                    'Nous sommes désolés pour le désagrément.' => 'We are sorry for the inconvenience.',
                ],
                'ar' => [
                    // Sample translations for demonstration
                    'Ceci est une réponse.' => 'هذا هو الرد.',
                    'Nous avons bien reçu votre réclamation.' => 'لقد تلقينا شكواك.',
                    'Votre problème sera résolu sous peu.' => 'سيتم حل مشكلتك قريبًا.',
                    'Merci pour votre patience.' => 'شكرا لصبرك.',
                    'Nous sommes désolés pour le désagrément.' => 'نأسف للإزعاج.',
                ]
            ];

            // Check if we have a direct translation for this text
            if (isset($translations[$targetLang][$originalDescription])) {
                $translatedDescription = $translations[$targetLang][$originalDescription];
            } else {
                // Fallback: In a real application, you would call a translation API here
                // For now, we'll just add a prefix to indicate it's a translation
                $translatedDescription = "[" . strtoupper($targetLang) . " TRANSLATION] " . $originalDescription;
            }
        }

        return $this->render('front_reponse/show.html.twig', [
            'reponse' => $reponse,
            'originalDescription' => $originalDescription,
            'translatedDescription' => $translatedDescription,
            'targetLang' => $targetLang
        ]);
    }

    #[Route('/{id}/translate/{lang}', name: 'front_reponse_translate', methods: ['GET'])]
    public function translate(Request $request, int $id, string $lang): Response
    {
        // Validate language
        if (!in_array($lang, ['original', 'en', 'ar'])) {
            throw $this->createNotFoundException('Language not supported');
        }

        // Redirect to the show page with the language parameter
        return $this->redirectToRoute('front_reponse_show', [
            'id' => $id,
            'lang' => $lang
        ]);
    }
}
