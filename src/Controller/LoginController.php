<?php
// src/Controller/LoginController.php
namespace App\Controller;
use App\Entity\Utilisateur;
use App\Service\FaceAuthService;
use App\Security\LoginSuccessHandler;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

use Symfony\Component\Routing\Annotation\Route;
use App\Security\GoogleAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // If user is already logged in, redirect based on role
        if ($this->getUser()) {
            return $this->redirectBasedOnRole();
        }
    
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
    
        // Handle AJAX login requests
        if ($request->isXmlHttpRequest()) {
            if ($error) {
                return new JsonResponse([
                    'success' => false,
                    'error' => $error->getMessageKey(),
                    'redirect' => false
                ], 401);
            }
    
            // For AJAX success, return the appropriate redirect URL
            $user = $this->getUser();
            $redirectRoute = in_array('ROLE_CLIENT', $user->getRoles()) 
                ? 'app_welcomeFront' 
                : 'app_welcome';
    
            return new JsonResponse([
                'success' => true,
                'redirect' => $this->generateUrl($redirectRoute)
            ]);
        }
    
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    private function redirectBasedOnRole(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        if (in_array('ROLE_CLIENT', $user->getRoles())) {
            return $this->redirectToRoute('app_welcomeFront');
        }
        
        return $this->redirectToRoute('app_welcome');
    }
    #[Route('/login/google', name: 'app_google_login')]
    #[Route('/login/google', name: 'app_google_login')]
    public function googleLogin(GoogleAuthenticator $googleAuthenticator, Request $request): Response
    {
        return $googleAuthenticator->start($request);
    }

    #[Route('/login/google-check', name: 'app_google_check')]
    public function googleCheck(): Response
    {
        // Cette méthode ne sera jamais atteinte car interceptée par l'authenticator
        return new RedirectResponse($this->generateUrl('app_login'));
    }
    // Ajoutez ces nouvelles routes à votre LoginController

    #[Route('/login/face', name: 'app_face_login')]
    public function faceLogin(Request $request, FaceAuthService $faceAuthService, LoggerInterface $logger): Response
    {
        if ($request->isMethod('POST')) {
            try {
                $faceData = $request->request->get('face_data');
                $logger->info('Face data received: '.substr($faceData, 0, 50).'...');
                
                if (!$faceData) {
                    throw new \Exception('Aucune donnée faciale reçue');
                }
    
                $matchingUsers = $faceAuthService->findMatchingUsers($faceData);
                $logger->info('Matching users found: '.count($matchingUsers));
        
        if (count($matchingUsers) === 1) {
            // Connexion automatique si un seul utilisateur correspond
            return $this->json([
                'success' => true,
                'redirect' => $this->generateUrl($this->getRedirectRoute($matchingUsers[0]))
            ]);
        } elseif (count($matchingUsers) > 1) {
            // Retourne les utilisateurs correspondants pour sélection
            $usersData = array_map(function($user) {
                return [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'fullName' => $user->getNom().' '.$user->getPrenom(),
                    'role' => in_array('ROLE_ADMIN', $user->getRoles()) ? 'Administrateur' : 'Client'
                ];
            }, $matchingUsers);
            
            return $this->json([
                'success' => true,
                'multiple' => true,
                'users' => $usersData
            ]);
        } else {
            return $this->json([
                'success' => false,
                'message' => 'Aucun utilisateur trouvé avec ce visage'
            ], 404);
        }
    }catch (\Exception $e) {
        $logger->error('Face login error: '.$e->getMessage());
        return $this->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString() // À supprimer en production
        ], 500);
    }
}

    return $this->render('login/face_login.html.twig');
}

#[Route('/login/face/select', name: 'app_face_login_select')]
public function selectUser(
    Request $request, 
    EntityManagerInterface $em,
    LoginSuccessHandler $loginSuccessHandler
): Response
{
    $userId = $request->request->get('user_id');
    $user = $em->getRepository(Utilisateur::class)->find($userId);
    
    if (!$user) {
        return $this->json(['success' => false, 'message' => 'Utilisateur non trouvé'], 404);
    }

    // Connectez l'utilisateur
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
}
private function getRedirectRoute($user): string
{
    return in_array('ROLE_CLIENT', $user->getRoles()) ? 
        'app_welcomeFront' : 
        'app_welcome';
}
}