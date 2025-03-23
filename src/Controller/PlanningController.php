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
use App\Entity\Notification;

#[Route('/planification')]
class PlanningController extends AbstractController
{
    #[Route('/', name: 'planning_index', methods: ['GET'])]
    public function index(PlanningRepository $repo): Response
    {
        $plannings = $repo->findAll();
        return $this->json($plannings, 200, [], ['groups' => ['planning:read']]);
    }

    #[Route('/my-planification', name: 'planning_mine', methods: ['GET'])]
    public function myPlanning(PlanningRepository $repo): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $plannings = $repo->findByUser($user);
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

        // Create notifications
        $note = $planning->getNote();
        $usersToNotify = array_filter([
            $note->getCreatedBy(),
            $note->getAssignedTo()
        ]);

        foreach ($usersToNotify as $user) {
            $notification = new Notification();
            $notification->setMessage(sprintf('New planification for note "%s"', $note->getTitle()))
                ->setRecipient($user)
                ->setPlanning($planning);

            $em->persist($notification);
        }

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
        $user = $this->getUser();
        $note = $planning->getNote();

        // Authorization check
        if (!$this->isGranted('ROLE_ADMIN')
            && $note->getCreatedBy()->getId() !== $user->getId()
            && $note->getAssignedTo()?->getId() !== $user->getId())
        {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

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
        $user = $this->getUser();
        $note = $planning->getNote();

        // Authorization check
        if (!$this->isGranted('ROLE_ADMIN')
            && $note->getCreatedBy()->getId() !== $user->getId()
            && $note->getAssignedTo()?->getId() !== $user->getId())
        {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        // Remove associated notifications first
        $notifications = $em->getRepository(Notification::class)->findBy(['planning' => $planning]);
        foreach ($notifications as $notification) {
            $em->remove($notification);
        }

        $em->remove($planning);
        $em->flush();

        return $this->json(['message' => 'Planning deleted successfully']);
    }
}
