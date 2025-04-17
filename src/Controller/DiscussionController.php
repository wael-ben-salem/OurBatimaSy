<?php

// src/Controller/DiscussionController.php
namespace App\Controller;

use App\Entity\Discussion;
use App\Entity\Plannification;
use App\Form\DiscussionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/discussion')]
class DiscussionController extends AbstractController
{
    #[Route('/send/{id}', name: 'app_discussion_send', methods: ['POST'])]
    public function sendMessage(Plannification $plannification, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $discussion = new Discussion();
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discussion->setPlannification($plannification);
            $discussion->setSender($this->getUser());
            $discussion->setRecipient($plannification->getIdTache()->getArtisan()->getArtisan());
            $discussion->setCreatedAt(new \DateTime());

            $em->persist($discussion);
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'content' => $discussion->getContent(),
                'createdAt' => $discussion->getCreatedAt()->format('d/m/Y H:i'),
                'sender' => $this->getUser()->getId()
            ]);
        }

        return new JsonResponse(['success' => false], 400);
    }

    #[Route('/get/{id}', name: 'app_discussion_get', methods: ['GET'])]
    public function getMessages(Plannification $plannification, EntityManagerInterface $em): JsonResponse
    {
        $messages = $em->getRepository(Discussion::class)->findBy(
            ['plannification' => $plannification],
            ['createdAt' => 'ASC']
        );

        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(), // ✅ Added message ID
                'content' => $message->getContent(),
                'createdAt' => $message->getCreatedAt()->format('d/m/Y H:i'),
                'sender' => $message->getSender()->getId()
            ];
        }

        return new JsonResponse($data);
    }
}
