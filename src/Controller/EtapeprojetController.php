<?php

namespace App\Controller;

use App\Entity\Etapeprojet;
use App\Form\EtapeprojetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/etapeprojet')]
final class EtapeprojetController extends AbstractController
{
    #[Route(name: 'app_etapeprojet_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $etapeprojets = $entityManager
            ->getRepository(Etapeprojet::class)
            ->findAll();

        return $this->render('etapeprojet/index.html.twig', [
            'etapeprojets' => $etapeprojets,
        ]);
    }

    #[Route('/new', name: 'app_etapeprojet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etapeprojet = new Etapeprojet();
        $form = $this->createForm(EtapeprojetType::class, $etapeprojet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etapeprojet);
            $entityManager->flush();

            return $this->redirectToRoute('app_etapeprojet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etapeprojet/new.html.twig', [
            'etapeprojet' => $etapeprojet,
            'form' => $form,
        ]);
    }

    #[Route('/{idEtapeprojet}', name: 'app_etapeprojet_show', methods: ['GET'])]
    public function show(Etapeprojet $etapeprojet): Response
    {
        return $this->render('etapeprojet/show.html.twig', [
            'etapeprojet' => $etapeprojet,
        ]);
    }

    #[Route('/{idEtapeprojet}/edit', name: 'app_etapeprojet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etapeprojet $etapeprojet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtapeprojetType::class, $etapeprojet);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'L\'étape du projet a été mise à jour avec succès.');
                return $this->redirectToRoute('app_etapeprojet_show', ['idEtapeprojet' => $etapeprojet->getIdEtapeprojet()]);
            } else {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            }
        }

        return $this->render('etapeprojet/edit.html.twig', [
            'etapeprojet' => $etapeprojet,
            'form' => $form->createView(), 
        ]);
    }

    #[Route('/{idEtapeprojet}', name: 'app_etapeprojet_delete', methods: ['POST'])]
    public function delete(Request $request, Etapeprojet $etapeprojet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etapeprojet->getIdEtapeprojet(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($etapeprojet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etapeprojet_index', [], Response::HTTP_SEE_OTHER);
    }
}
