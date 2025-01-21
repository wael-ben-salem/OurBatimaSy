<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NoteController extends AbstractController
{
    #[Route('/note', name: 'app_note_index', methods: ['GET'])]
    public function index(NoteRepository $noteRepository): Response
    {
        $notes = $noteRepository->findAll();
        return $this->json($notes);
    }

    #[Route('/note/new', name: 'app_note_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Decode the incoming JSON data
        $data = json_decode($request->getContent(), true);

        // Create a new Note object
        $note = new Note();
        $note->setTitle($data['title'] ?? null);
        $note->setContent($data['content'] ?? null);
        $note->setDate(new \DateTimeImmutable());

        // Set the currently authenticated user as the creator
        $note->setCreatedBy($this->getUser());

        // Persist the note to the database
        $em->persist($note);
        $em->flush();

        // Respond with success
        return $this->json(['message' => 'Note created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/note/{id}', name: 'app_note_show', methods: ['GET'])]
    public function show(Note $note): Response
    {
        // Return the note as a JSON response
        return $this->json($note);
    }

    #[Route('/note/{id}', name: 'app_note_edit', methods: ['PUT'])]
    public function edit(Request $request, Note $note, EntityManagerInterface $em): Response
    {
        // Decode incoming data
        $data = json_decode($request->getContent(), true);

        // Update the note's properties if provided
        $note->setTitle($data['title'] ?? $note->getTitle());
        $note->setContent($data['content'] ?? $note->getContent());

        // Save the changes to the database
        $em->flush();

        // Respond with success
        return $this->json(['message' => 'Note updated successfully']);
    }

    #[Route('/note/{id}', name: 'app_note_delete', methods: ['DELETE'])]
    public function delete(Note $note, EntityManagerInterface $em): Response
    {
        // Remove the note from the database
        $em->remove($note);
        $em->flush();

        // Respond with success
        return $this->json(['message' => 'Note deleted successfully']);
    }
}
