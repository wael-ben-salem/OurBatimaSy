<?php

// src/Controller/PlanningController.php
namespace App\Controller;

use App\Entity\Planning;
use App\Entity\Note;
use App\Repository\PlanningRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/planification')]
class PlanningController extends AbstractController
{
    #[Route('/', name: 'planning_index', methods: ['GET'])]
    public function index(PlanningRepository $repo): Response
    {
        $plannings = $repo->findAll();
        return $this->json($plannings, 200, [], ['groups' => ['planning:read']]);
    }

    #[Route('/new', name: 'planning_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $planning = new Planning();
        $note = $em->getReference(Note::class, $data['note_id']);

        $planning->setNote($note)
            ->setDatePlanifie(new \DateTime($data['date_planifie']))
            ->setHeureDebut(new \DateTime($data['heure_debut']))
            ->setHeureFin(new \DateTime($data['heure_fin']))
            ->setStatut($data['statut']);

        $em->persist($planning);
        $em->flush();

        return $this->json($planning, 201, [], ['groups' => ['planning:read']]);
    }

    #[Route('/{id}', name: 'planning_show', methods: ['GET'])]
    public function show(Planning $planning): Response
    {
        return $this->json($planning, 200, [], ['groups' => ['planning:read']]);
    }

    #[Route('/{id}', name: 'planning_edit', methods: ['PUT'])]
    public function edit(Planning $planning, Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['note_id'])) {
            $note = $em->getReference(Note::class, $data['note_id']);
            $planning->setNote($note);
        }

        $planning->setDatePlanifie(new \DateTime($data['date_planifie'] ?? $planning->getDatePlanifie()->format('Y-m-d')))
            ->setHeureDebut(new \DateTime($data['heure_debut'] ?? $planning->getHeureDebut()->format('H:i:s')))
            ->setHeureFin(new \DateTime($data['heure_fin'] ?? $planning->getHeureFin()->format('H:i:s')))
            ->setStatut($data['statut'] ?? $planning->getStatut());

        $em->flush();

        return $this->json($planning, 200, [], ['groups' => ['planning:read']]);
    }

    #[Route('/{id}', name: 'planning_delete', methods: ['DELETE'])]
    public function delete(Planning $planning, EntityManagerInterface $em): Response
    {
        $em->remove($planning);
        $em->flush();
        return $this->json(['message' => 'Planning deleted successfully']);
    }
}