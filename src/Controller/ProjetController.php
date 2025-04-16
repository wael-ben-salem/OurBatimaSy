<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Entity\Client;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projet')]
final class ProjetController extends AbstractController
{
    #[Route(name: 'app_projet_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $projets = $entityManager
            ->getRepository(Projet::class)
            ->findAll();

        return $this->render('projet/index.html.twig', [
            'projets' => $projets,
        ]);
    }
    #[Route('/new', name: 'app_projet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $projet->setDatecreation(new \DateTime());
    
                $emailClient = $form->get('nomClient')->getData();
    
                // Only try to set a client if an email was provided
                if (!empty($emailClient)) {
                    $client = $entityManager->getRepository(Client::class)
                        ->createQueryBuilder('c')
                        ->join('c.client', 'u')
                        ->where('u.email = :email')
                        ->setParameter('email', $emailClient)
                        ->getQuery()
                        ->getOneOrNullResult();
    
                    if ($client) {
                        $projet->setIdClient($client);
                    } else {
                        $this->addFlash('error', 'Client with this email not found.');
                        return $this->redirectToRoute('app_projet_new');
                    }
                }
    
                $entityManager->persist($projet);
                $entityManager->flush();
    
                $this->addFlash('success', 'Project successfully created.');
    
                return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Please correct the errors in the form.');
            }
        }
    
        return $this->render('projet/new.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }
    
 
    

    #[Route('/show/{idProjet}', name: 'app_projet_show', methods: ['GET'])]
    public function show(Projet $projet): Response
    {
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }
    #[Route('/{idProjet}/edit', name: 'app_projet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        // Store the original client email before handling the form
        $originalClientEmail = $projet->getIdClient() ? $projet->getIdClient()->getClient()->getEmail() : null;
        
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $emailClient = $form->get('nomClient')->getData();
                
                // Handle client association
                if (!empty($emailClient)) {
                    // Only search for client if email changed
                    if ($emailClient !== $originalClientEmail) {
                        $client = $entityManager->getRepository(Client::class)
                            ->createQueryBuilder('c')
                            ->join('c.client', 'u')
                            ->where('u.email = :email')
                            ->setParameter('email', $emailClient)
                            ->getQuery()
                            ->getOneOrNullResult();
    
                        if ($client) {
                            $projet->setIdClient($client);
                        } else {
                            $this->addFlash('error', 'Client with this email not found.');
                            return $this->redirectToRoute('app_projet_edit', ['idProjet' => $projet->getIdProjet()]);
                        }
                    }
                } else {
                    // If email field is empty, remove client association
                    $projet->setIdClient(null);
                }
    
                $entityManager->flush();
                $this->addFlash('success', 'Project updated successfully.');
                return $this->redirectToRoute('app_projet_show', ['idProjet' => $projet->getIdProjet()], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Please correct the errors in the form.');
            }
        }
    
        // Pre-fill the client email in the form
        $form->get('nomClient')->setData($originalClientEmail);
    
        return $this->render('projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(), // Make sure to call createView()
        ]);
    }

    #[Route('/{idProjet}', name: 'app_projet_delete', methods: ['POST'])]
    public function delete(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projet->getIdProjet(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($projet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
    }

    //FRONT OFFICE
    #[Route('/front', name: 'app_projet_front_index', methods: ['GET'])]
    public function frontIndex(EntityManagerInterface $entityManager): Response
    {
        $projets = $entityManager
            ->getRepository(Projet::class)
            ->findAll();

        return $this->render('projetFront/index.html.twig', [
            'projets' => $projets,
        ]);
    }

    #[Route('/front/new', name: 'app_projet_front_new', methods: ['GET', 'POST'])]
    public function frontNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet, [
            'action' => $this->generateUrl('app_projet_front_new'),
        ]);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $projet->setDatecreation(new \DateTime());
            
            // Handle client association if needed
            $emailClient = $form->get('nomClient')->getData();
            if (!empty($emailClient)) {
                $client = $entityManager->getRepository(Client::class)
                    ->createQueryBuilder('c')
                    ->join('c.client', 'u')
                    ->where('u.email = :email')
                    ->setParameter('email', $emailClient)
                    ->getQuery()
                    ->getOneOrNullResult();
                
                if ($client) {
                    $projet->setIdClient($client);
                }
            }
            
            $entityManager->persist($projet);
            $entityManager->flush();
            
            $this->addFlash('success', 'Your project has been submitted successfully!');
            return $this->redirectToRoute('app_projet_front_index');
        }
    
        return $this->render('projetFront/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/front/{idProjet}', name: 'app_projet_front_show', methods: ['GET'])]
    public function frontShow(Projet $projet): Response
    {
        return $this->render('projetFront/show.html.twig', [
            'projet' => $projet,
        ]);
    }
    
}
