<?php
// src/Controller/NoteController.php
namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Service\RecommendationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    #[Route('/notes', name: 'app_note_index')]
    public function index(NoteRepository $noteRepository): Response
    {
        return $this->render('note/index.html.twig', [
            'notes' => $noteRepository->findAll()
        ]);
    }

    #[Route('/my-notes', name: 'app_my_notes')]
    public function myNotes(NoteRepository $noteRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('note/my_notes.html.twig', [
            'notes' => $noteRepository->findByUser($user)
        ]);
    }

    #[Route('/note/new', name: 'app_note_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        RecommendationService $recommendationService
    ): Response {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) {
                return $this->redirectToRoute('app_login');
            }

            $note->setCreatedBy($user);
            $note->setDate(new \DateTimeImmutable());

            if (!$note->getAssignedTo()) {
                $bestUser = $recommendationService->getBestPerformingUser();
                if ($bestUser) {
                    $note->setAssignedTo($bestUser);
                }
            }

            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('app_my_notes');
        }

        return $this->render('note/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/note/{id}', name: 'app_note_show')]
    public function show(Note $note): Response
    {
        return $this->render('note/show.html.twig', [
            'note' => $note
        ]);
    }

    #[Route('/note/{id}/edit', name: 'app_note_edit')]
    public function edit(Request $request, Note $note, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') &&
            $note->getCreatedBy()->getId() !== $user->getId() &&
            $note->getAssignedTo()?->getId() !== $user->getId())
        {
            $this->addFlash('error', 'Access denied');
            return $this->redirectToRoute('app_note_index');
        }

        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_my_notes');
        }

        return $this->render('note/edit.html.twig', [
            'form' => $form->createView(),
            'note' => $note
        ]);
    }

    #[Route('/note/{id}/delete', name: 'app_note_delete')]
    public function delete(Note $note, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') &&
            $note->getCreatedBy()->getId() !== $user->getId() &&
            $note->getAssignedTo()?->getId() !== $user->getId())
        {
            $this->addFlash('error', 'Access denied');
            return $this->redirectToRoute('app_note_index');
        }

        $em->remove($note);
        $em->flush();

        return $this->redirectToRoute('app_my_notes');
    }
}
