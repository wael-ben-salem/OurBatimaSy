<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Security\LoginSuccessHandler;

use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement sans face_data
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_face_upload', ['id' => $user->getId()]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/face-upload/{id}', name: 'app_face_upload')]
    public function faceUpload(Utilisateur $user): Response
    {
        return $this->render('registration/face_upload.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/save-face/{id}', name: 'app_save_face', methods: ['POST'])]
    public function saveFace(
        Request $request,
        Utilisateur $user,
        EntityManagerInterface $entityManager,
        LoginSuccessHandler $loginSuccessHandler
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['face_data'])) {
            return $this->json(['success' => false, 'message' => 'No face data provided'], 400);
        }
    
        try {
            $user->setFaceData($data['face_data'])
                 ->setFaceDataUpdatedAt(new \DateTime());
            
            $entityManager->flush();
    
            // Créez un token pour l'utilisateur
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
    
            // Utilisez le LoginSuccessHandler pour la redirection
            $response = $loginSuccessHandler->onAuthenticationSuccess(
                $request,
                $token
            );
    
            return $this->json([
                'success' => true,
                'redirect' => $response->getTargetUrl()
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error saving face data: ' . $e->getMessage()
            ], 500);
        }
    }
}