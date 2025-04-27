<?php

namespace App\Controller;

use App\Entity\Plannification;
use App\Form\PlannificationType;
use Doctrine\ORM\EntityManagerInterface;
use Nucleos\DompdfBundle\Exception\PdfException;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Discussion;
use App\Form\DiscussionType;
use App\Entity\SavedPlannification;
use App\Entity\PlanifNotifications;

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

            // Create notifications
            $currentUser = $this->getUser();
            $artisanUser = $plannification->getIdTache()->getArtisan()->getArtisan();

            $this->createNotification(
                $currentUser,
                "You created a new plannification: " . $plannification->getIdPlannification(),
                $plannification,
                $entityManager
            );

            $this->createNotification(
                $artisanUser,
                "New plannification assigned to you: " . $plannification->getIdPlannification(),
                $plannification,
                $entityManager
            );

            return $this->redirectToRoute('app_plannification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plannification/new.html.twig', [
            'plannification' => $plannification,
            'form' => $form,
        ]);
    }

    private function createNotification($recipient, $message, $plannification, $em)
    {
        $notification = new PlanifNotifications();
        $notification->setRecipient($recipient);
        $notification->setMessage($message);
        $notification->setPlannification($plannification);
        $em->persist($notification);
        $em->flush();
    }

    #[Route('/saved', name: 'app_plannification_saved', methods: ['GET'])]
    public function savedPlans(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $savedPlans = $entityManager->getRepository(SavedPlannification::class)->findBy(
            ['user' => $user],
            ['id' => 'DESC']
        );

        return $this->render('plannification/saved.html.twig', [
            'saved_plans' => $savedPlans
        ]);
    }

    /**
     * @throws PdfException
     */
    #[Route('/plannification/{idPlannification}/pdf', name: 'app_plannification_pdf')]
    public function pdf(
        Plannification $plannification,
        DompdfWrapperInterface $dompdf
    ): Response {
        $html = $this->renderView('plannification/pdf.html.twig', [
            'plannification' => $plannification
        ]);

        return new Response(
            $dompdf->getPdf($html),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="plannification-%d.pdf"', $plannification->getIdPlannification())
            ]
        );
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
        if ($this->isCsrfTokenValid('delete' . $plannification->getIdPlannification(), $request->request->get('_token'))) {
            $entityManager->remove($plannification);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_plannification_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{idPlannification}/save', name: 'app_plannification_save', methods: ['POST'])]
    public function save(Plannification $plannification, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $existingSave = $entityManager->getRepository(SavedPlannification::class)->findOneBy([
            'user' => $user,
            'plannification' => $plannification
        ]);

        if (!$existingSave) {
            $savedPlan = new SavedPlannification();
            $savedPlan->setUser($user);
            $savedPlan->setPlannification($plannification);

            $entityManager->persist($savedPlan);
            $entityManager->flush();

            $this->addFlash('success', 'Plannification saved successfully!');
        }

        return $this->redirectToRoute('app_plannification_show', [
            'idPlannification' => $plannification->getIdPlannification()
        ]);
    }

    #[Route('/{idPlannification}/unsave', name: 'app_plannification_unsave', methods: ['POST'])]
    public function unsave(Plannification $plannification, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $savedPlan = $entityManager->getRepository(SavedPlannification::class)->findOneBy([
            'user' => $user,
            'plannification' => $plannification
        ]);

        if ($savedPlan) {
            $entityManager->remove($savedPlan);
            $entityManager->flush();
            $this->addFlash('success', 'Plannification unsaved successfully!');
        }

        return $this->redirectToRoute('app_plannification_show', [
            'idPlannification' => $plannification->getIdPlannification()
        ]);
    }
}
