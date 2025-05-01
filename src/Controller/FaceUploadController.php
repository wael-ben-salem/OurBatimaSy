<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Security\LoginSuccessHandler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class FaceUploadController extends AbstractController
{
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
