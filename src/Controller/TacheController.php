<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use App\Entity\Constructeur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/tache')]
class TacheController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_tache_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $taches = $entityManager
            ->getRepository(Tache::class)
            ->findAll();

        return $this->render('tache/index.html.twig', [
            'taches' => $taches,
        ]);
    }

    #[Route('/new', name: 'app_tache_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the currently authenticated user
            $user = $this->security->getUser();

            // Find the Constructeur entity related to the current user
            $constructeur = $entityManager->getRepository(Constructeur::class)->findOneBy(['constructeur' => $user]);

            if (!$constructeur) {
                throw $this->createAccessDeniedException('Only constructeurs can create tasks.');
            }

            // Associate the task with the constructeur
            $tache->setConstructeur($constructeur);

            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/new.html.twig', [
            'tache' => $tache,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{idTache}', name: 'app_tache_show', methods: ['GET'])]
    public function show(Tache $tache): Response
    {
        return $this->render('tache/show.html.twig', [
            'tache' => $tache,
        ]);
    }

    #[Route('/{idTache}/edit', name: 'app_tache_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/edit.html.twig', [
            'tache' => $tache,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{idTache}', name: 'app_tache_delete', methods: ['POST'])]
    public function delete(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tache->getIdTache(), $request->request->get('_token'))) {
            $entityManager->remove($tache);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
    }
}
