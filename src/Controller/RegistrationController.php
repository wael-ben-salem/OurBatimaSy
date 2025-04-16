<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Artisan;
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
            // Hasher le mot de passe à partir du champ `plainPassword`
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);

            // Définir le rôle par défaut
            $user->setRole('artisan');

            // Persister l'utilisateur
            $entityManager->persist($user);

            // Si le rôle est artisan, créer un objet Artisan lié
            if ($user->getRole() === 'artisan') {
                $artisan = new Artisan();
                $artisan->setArtisan($user);
                $artisan->setSpecialite('Default Specialite');
                $artisan->setSalaireHeure('0.00');
                $entityManager->persist($artisan);
            }

            // Sauvegarder dans la base de données
            $entityManager->flush();

            return $this->redirectToRoute('app_welcome');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
