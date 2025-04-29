<?php

// src/Controller/ChatController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\GeminiService;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    public function index(): Response
    {
        return $this->render('chat/index.html.twig');
    }

    #[Route('/chat/ask', name: 'app_chat_ask', methods: ['POST'])]
    public function ask(Request $request, GeminiService $gemini): Response
    {
        $question = $request->request->get('question');
        $answer = $gemini->getAnswer($question);

        return $this->json([
            'question' => $question,
            'answer' => $answer
        ]);
    }
}