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
                // First check if a Utilisateur exists with this email and has role 'Client'
                $utilisateur = $entityManager->getRepository(Utilisateur::class)
                    ->findOneBy([
                        'email' => $emailClient,
                        'role' => 'Client' // Using 'Client' as per your default
                    ]);

                if ($utilisateur) {
                    // Then find the Client associated with this Utilisateur
                    // Using 'client' as the property name in Client entity
                    $client = $entityManager->getRepository(Client::class)
                        ->findOneBy(['client' => $utilisateur]);

                    if ($client) {
                        $projet->setIdClient($client);
                    } else {
                        // Create a new Client if one doesn't exist
                        $client = new Client();
                        $client->setClient($utilisateur); // Using setClient() method
                        
                        // Optionally set other client properties if needed
                        // $client->setSomeProperty('some value');
                        
                        $entityManager->persist($client);
                        $entityManager->flush();
                        
                        $projet->setIdClient($client);
                    }
                } else {
                    $this->addFlash('error', 'Aucun compte utilisateur avec le rôle client n\'a été trouvé avec cet e-mail.');
                    return $this->redirectToRoute('app_projet_new');
                }
            }

            $entityManager->persist($projet);
            $entityManager->flush();

            $this->addFlash('success', 'Project successfully created.');
            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        } else {
            $this->addFlash('error', 'Veuillez vérifier à nouveau les champs.');
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
        // Store the original client before handling the form
        $originalClient = $projet->getIdClient();
        
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $emailClient = $form->get('nomClient')->getData();
            
            // Only process client if email changed or was removed
            if ($emailClient !== ($originalClient ? $originalClient->getClient()->getEmail() : null)) {
                if (!empty($emailClient)) {
                    $utilisateur = $entityManager->getRepository(Utilisateur::class)
                        ->findOneBy([
                            'email' => $emailClient,
                            'role' => 'Client'
                        ]);
    
                    if (!$utilisateur) {
                        $this->addFlash('error', 'Aucun compte utilisateur avec le rôle client trouvé avec cet e-mail.');
                        return $this->redirectToRoute('app_projet_edit', ['idProjet' => $projet->getIdProjet()]);
                    }
    
                    $client = $entityManager->getRepository(Client::class)
                        ->findOneBy(['client' => $utilisateur]);
    
                    if (!$client) {
                        $client = new Client();
                        $client->setClient($utilisateur);
                        $entityManager->persist($client);
                        // Don't flush yet - wait for the main flush
                    }
    
                    $projet->setIdClient($client);
                } else {
                    $projet->setIdClient(null);
                }
            }
    
            $entityManager->flush();
            $this->addFlash('success', 'Projet mis à jour avec succès.');
            return $this->redirectToRoute('app_projet_show', ['idProjet' => $projet->getIdProjet()]);
        }
    
        return $this->render('projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
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
        
        $emailClient = $form->get('nomClient')->getData();
        
        // Handle client association if needed
        if (!empty($emailClient)) {
            // First check if a Utilisateur exists with this email and has role 'Client'
            $utilisateur = $entityManager->getRepository(Utilisateur::class)
                ->findOneBy([
                    'email' => $emailClient,
                    'role' => 'Client'
                ]);

            if ($utilisateur) {
                // Then find the Client associated with this Utilisateur
                $client = $entityManager->getRepository(Client::class)
                    ->findOneBy(['client' => $utilisateur]);

                if ($client) {
                    $projet->setIdClient($client);
                } else {
                    // Create a new Client if one doesn't exist
                    $client = new Client();
                    $client->setClient($utilisateur);
                    $entityManager->persist($client);
                    $entityManager->flush();
                    
                    $projet->setIdClient($client);
                }
            } else {
                $this->addFlash('error', 'Aucun compte utilisateur avec le rôle client n\'a été trouvé avec cet e-mail.');
                return $this->redirectToRoute('app_projet_front_new');
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