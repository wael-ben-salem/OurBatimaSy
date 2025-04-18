<?php

// src/Controller/PlannificationController.php
namespace App\Controller;

use App\Entity\Plannification;
use App\Form\PlannificationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Add these use statements
use App\Entity\Discussion;
use App\Form\DiscussionType;

#[Route('/plannification')]
class PlannificationController extends AbstractController
{
    #[Route('/', name: 'app_plannification_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $plannifications = $entityManager
            ->getRepository(Plannification::class)
            ->findAll();

        return $this->render('plannification/index.html.twig', [
            'plannifications' => $plannifications,
        ]);
    }

    #[Route('/new', name: 'app_plannification_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $plannification = new Plannification();
        $form = $this->createForm(PlannificationType::class, $plannification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($plannification);
            $entityManager->flush();

            return $this->redirectToRoute('app_plannification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plannification/new.html.twig', [
            'plannification' => $plannification,
            'form' => $form,
        ]);
    }

    #[Route('/{idPlannification}', name: 'app_plannification_show', methods: ['GET', 'POST'])]
    public function show(Plannification $plannification, Request $request, EntityManagerInterface $entityManager): Response
    {
        $discussion = new Discussion();
        $form = $this->createForm(DiscussionType::class, $discussion);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $discussion->setPlannification($plannification);
            $discussion->setSender($this->getUser());
            $discussion->setRecipient($plannification->getIdTache()->getArtisan()->getArtisan());
            $discussion->setCreatedAt(new \DateTime());

            $entityManager->persist($discussion);
            $entityManager->flush();

            return $this->redirectToRoute('app_plannification_show', [
                'idPlannification' => $plannification->getIdPlannification()
            ]);
        }

        $messages = $entityManager->getRepository(Discussion::class)->findBy(
            ['plannification' => $plannification],
            ['createdAt' => 'ASC']
        );

        return $this->render('plannification/show.html.twig', [
            'plannification' => $plannification,
            'messages' => $messages,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{idPlannification}/edit', name: 'app_plannification_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plannification $plannification, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlannificationType::class, $plannification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_plannification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plannification/edit.html.twig', [
            'plannification' => $plannification,
            'form' => $form,
        ]);
    }

    #[Route('/{idPlannification}/delete', name: 'app_plannification_delete', methods: ['POST'])]
    public function delete(Request $request, Plannification $plannification, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plannification->getIdPlannification(), $request->request->get('_token'))) {
            $entityManager->remove($plannification);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_plannification_index', [], Response::HTTP_SEE_OTHER);
    }
}
