<?php
// src/Controller/PlanningController.php
namespace App\Controller;

use App\Entity\Planning;
use App\Entity\Notification;
use App\Entity\Message;
use App\Form\PlanningType;
use App\Repository\PlanningRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/planification')]
class PlanningController extends AbstractController
{
    #[Route('/', name: 'planning_index')]
    public function index(PlanningRepository $repo): Response
    {
        $user = $this->getUser();
        $plannings = $repo->findAll();

        return $this->render('planning/index.html.twig', [
            'plannings' => $plannings,
            'savedPlannings' => $user ? $user->getSavedPlannings() : []
        ]);
    }

    #[Route('/new', name: 'planning_new')]
    public function new(Request $request, EntityManagerInterface $em, NoteRepository $noteRepo): Response
    {
        $user = $this->getUser();
        $notes = $noteRepo->findByUser($user);

        $planning = new Planning();
        $form = $this->createForm(PlanningType::class, $planning, [
            'user_notes' => $notes
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            $em->persist($planning);
            $em->flush();

            return $this->redirectToRoute('planning_index');
        }

        return $this->render('planning/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/toggle-save', name: 'planning_toggle_save')]
    public function toggleSave(Planning $planning, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($user->getSavedPlannings()->contains($planning)) {
            $user->removeSavedPlanning($planning);
        } else {
            $user->addSavedPlanning($planning);
        }

        $em->flush();
        return $this->redirectToRoute('planning_index');
    }

    #[Route('/{id}', name: 'planning_show')]
    public function show(Planning $planning): Response
    {
        return $this->render('planning/show.html.twig', [
            'planning' => $planning
        ]);
    }

    #[Route('/{id}/edit', name: 'planning_edit')]
    public function edit(Request $request, Planning $planning, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $note = $planning->getNote();

        if (!$this->isGranted('ROLE_ADMIN') &&
            $note->getCreatedBy()->getId() !== $user->getId() &&
            $note->getAssignedTo()?->getId() !== $user->getId())
        {
            $this->addFlash('error', 'Access denied');
            return $this->redirectToRoute('planning_index');
        }

        $form = $this->createForm(PlanningType::class, $planning, [
            'user_notes' => [$note] // <-- Step 2 change
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('planning_show', ['id' => $planning->getId()]);
        }

        return $this->render('planning/edit.html.twig', [
            'form' => $form->createView(),
            'planning' => $planning
        ]);
    }

    #[Route('/{id}/delete', name: 'planning_delete')]
    public function delete(Planning $planning, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $note = $planning->getNote();

        if (!$this->isGranted('ROLE_ADMIN') &&
            $note->getCreatedBy()->getId() !== $user->getId() &&
            $note->getAssignedTo()?->getId() !== $user->getId())
        {
            $this->addFlash('error', 'Access denied');
            return $this->redirectToRoute('planning_index');
        }

        $em->remove($planning);
        $em->flush();
        return $this->redirectToRoute('planning_index');
    }

    #[Route('/{id}/send-message', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request, Planning $planning, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $note = $planning->getNote();

        if (!$user || ($user !== $note->getCreatedBy() && $user !== $note->getAssignedTo())) {
            $this->addFlash('error', 'Access denied');
            return $this->redirectToRoute('planning_show', ['id' => $planning->getId()]);
        }

        $content = $request->request->get('content');
        if (empty($content)) {
            $this->addFlash('error', 'Message cannot be empty');
            return $this->redirectToRoute('planning_show', ['id' => $planning->getId()]);
        }

        $message = new Message();
        $message->setContent($content)
            ->setSender($user)
            ->setPlanning($planning);

        $em->persist($message);
        $em->flush();

        return $this->redirectToRoute('planning_show', ['id' => $planning->getId()]);
    }
}
