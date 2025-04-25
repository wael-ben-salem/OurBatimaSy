<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Repository\CustomUtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, CustomUtilisateurRepository $userRepository): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the role based on the form selection
            $role = $form->get('role')->getData();
            $user->setRole($role);
            
            // Set default status and confirmation
            $user->setStatut('Active');
            $user->setIsconfirmed(true);
            
            // Use our custom repository to create the user
            $userRepository->createUser($user, $form->get('plainPassword')->getData());

            return $this->redirectToRoute('app_welcome');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}