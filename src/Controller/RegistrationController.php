<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Constructeur; // Changed from Artisan to Constructeur
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the plain password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Set default role to constructeur
            $user->setRole('constructeur');

            // Persist the user first
            $entityManager->persist($user);

            // Create and associate Constructeur entity
            $constructeur = new Constructeur();
            $constructeur->setConstructeur($user); // Make sure this matches your Constructeur entity's method
            $constructeur->setSpecialite('Default Speciality'); // Set default or get from form
            $constructeur->setSalaireHeure('0.00'); // Default value

            $entityManager->persist($constructeur);

            $entityManager->flush();

            return $this->redirectToRoute('app_welcome');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}