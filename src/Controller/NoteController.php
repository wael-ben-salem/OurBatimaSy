<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
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

    #[Route('/note/new', name: 'app_note_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
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
        $data = json_decode($request->getContent(), true);

        // Basic validation
        if (!$note) {
            return $this->json(['error' => 'Note not found'], Response::HTTP_NOT_FOUND);
        }

        // Update fields
        $note->setTitle($data['title'] ?? $note->getTitle());
        $note->setContent($data['content'] ?? $note->getContent());

        $em->flush();

        return $this->json(['message' => 'Note updated successfully']);
    }

    #[Route('/note/{id}', name: 'app_note_delete', methods: ['DELETE'])]
    public function delete(Note $note, EntityManagerInterface $em): Response
    {
        if (!$note) {
            return $this->json(['error' => 'Note not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($note);
        $em->flush();

        return $this->json(['message' => 'Note deleted successfully']);
    }
}
