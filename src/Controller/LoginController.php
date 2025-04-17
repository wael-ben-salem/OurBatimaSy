<?php
// src/Controller/LoginController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
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
}