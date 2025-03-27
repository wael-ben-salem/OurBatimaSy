<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Repository\NoteRepository;
use App\Service\RecommendationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    #[Route('/note', name: 'app_note_index', methods: ['GET'])]
    public function index(NoteRepository $noteRepository): Response
    {
        $notes = $noteRepository->findAll();
        return $this->json($notes, Response::HTTP_OK, [], ['groups' => ['note:read', 'note:details']]);
    }

    #[Route('/my-notes', name: 'app_my_notes', methods: ['GET'])]
    public function myNotes(NoteRepository $noteRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $notes = $noteRepository->findByUser($user);
        return $this->json($notes, Response::HTTP_OK, [], ['groups' => ['note:read', 'note:details']]);
    }

    #[Route('/note/new', name: 'app_note_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        RecommendationService $recommendationService
    ): Response {
        $data = json_decode($request->getContent(), true);

        $note = new Note();
        $note->setTitle($data['title'] ?? null);
        $note->setContent($data['content'] ?? null);
        $note->setDate(new \DateTimeImmutable());

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'User is not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $note->setCreatedBy($user);

        if (!isset($data['assignedTo'])) {
            $bestUser = $recommendationService->getBestPerformingUser();
            if ($bestUser) {
                $note->setAssignedTo($bestUser);
            }
        } else {
            $assignedUser = $em->getReference(User::class, $data['assignedTo']);
            $note->setAssignedTo($assignedUser);
        }

        $em->persist($note);
        $em->flush();

        return $this->json(['message' => 'Note created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/note/{id}', name: 'app_note_show', methods: ['GET'])]
    public function show(Note $note): Response
    {
        return $this->json($note, Response::HTTP_OK, [], ['groups' => ['note:read', 'note:details']]);
    }

    #[Route('/note/{id}', name: 'app_note_edit', methods: ['PUT'])]
    public function edit(Request $request, Note $note, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$this->isGranted('ROLE_ADMIN')
            && $note->getCreatedBy()->getId() !== $user->getId()
            && $note->getAssignedTo()?->getId() !== $user->getId())
        {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        $note->setTitle($data['title'] ?? $note->getTitle());
        $note->setContent($data['content'] ?? $note->getContent());

        if (isset($data['assignedTo'])) {
            $assignedUser = $em->getReference(User::class, $data['assignedTo']);
            $note->setAssignedTo($assignedUser);
        } else {
            $note->setAssignedTo(null);
        }

        $em->flush();

        return $this->json(['message' => 'Note updated successfully']);
    }

    #[Route('/note/{id}', name: 'app_note_delete', methods: ['DELETE'])]
    public function delete(Note $note, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$this->isGranted('ROLE_ADMIN')
            && $note->getCreatedBy()->getId() !== $user->getId()
            && $note->getAssignedTo()?->getId() !== $user->getId())
        {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $em->remove($note);
        $em->flush();

        return $this->json(['message' => 'Note deleted successfully']);
    }
}
