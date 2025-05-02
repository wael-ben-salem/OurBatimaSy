<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Client;
use App\Form\CompleteProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\EmailService;

class ProfileController extends AbstractController
{
    #[Route('/complete-profile', name: 'app_complete_profile')]
    public function completeProfile(
        Request $request,
        EntityManagerInterface $em,
        EmailService $emailService,

        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(CompleteProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe si fourni
            if ($form->get('plainPassword')->getData()) {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            if (in_array('ROLE_CLIENT', $user->getRoles())) {
                $existingClient = $em->getRepository(Client::class)->findOneBy(['client' => $user]);
                
                if (!$existingClient) {
                    $client = new Client();
                    $client->setClient($user);
                    $em->persist($client);
                }
            }

            $em->flush();
            // Envoi de l'email de bienvenue
            if (!$emailService->sendConfirmationEmail(
                $user->getEmail(),
                $user->getFullName() // ou getUsername() selon votre entité
            )) {
                $this->addFlash('warning', 'L\'email de confirmation n\'a pas pu être envoyé.');
            }

            return $this->redirectToRoute('app_face_upload', ['id' => $user->getId()]);
        }

        return $this->render('registration/complete_profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}