<?php
// src/Controller/LoginController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Security\GoogleAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
}