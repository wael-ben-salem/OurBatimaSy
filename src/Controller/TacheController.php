<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use App\Entity\Constructeur;
use App\Service\ProfanityFilterService;
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
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ProfanityFilterService $profanityFilter
    ): Response {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();

            $constructeur = $entityManager
                ->getRepository(Constructeur::class)
                ->findOneBy(['constructeur' => $user]);

            if (!$constructeur) {
                throw $this->createAccessDeniedException('Only constructeurs can create tasks.');
            }

            // Filter the description
            $originalDescription = $tache->getDescription();
            $filteredDescription = $profanityFilter->filterText($originalDescription);
            $tache->setDescription($filteredDescription);

            $tache->setConstructeur($constructeur);

            $entityManager->persist($tache);
            $entityManager->flush();

            if ($originalDescription !== $filteredDescription) {
                $this->addFlash('warning', 'Your task description contained inappropriate language and has been filtered.');
            }

            return $this->redirectToRoute('app_plannification_new', [
                'tache_id' => $tache->getIdTache()
            ]);
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
    public function edit(
        Request $request,
        Tache $tache,
        EntityManagerInterface $entityManager,
        ProfanityFilterService $profanityFilter
    ): Response {
        $originalDescription = $tache->getDescription();

        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($tache->getDescription() !== $originalDescription) {
                $filteredDescription = $profanityFilter->filterText($tache->getDescription());
                $tache->setDescription($filteredDescription);

                if ($originalDescription !== $filteredDescription) {
                    $this->addFlash('warning', 'Your task description contained inappropriate language and has been filtered.');
                }
            }

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