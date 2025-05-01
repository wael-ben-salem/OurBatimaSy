<?php
// src/Controller/LoginController.php
namespace App\Controller;
use App\Entity\Utilisateur;
use App\Service\FaceAuthService;
use App\Security\LoginSuccessHandler;
use App\Service\PasswordResetService;


use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Security\GoogleAuthenticator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectBasedOnRole();
        }
    
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
    
        if ($request->isXmlHttpRequest()) {
            if ($error) {
                return new JsonResponse([
                    'success' => false,
                    'error' => $error->getMessageKey(),
                    'redirect' => false
                ], 401);
            }
    
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

    #[Route('/login/face', name: 'app_face_login')]
    public function faceLogin(Request $request, FaceAuthService $faceAuthService, LoggerInterface $logger): Response
    {
        if ($request->isMethod('POST')) {
            try {
                $faceData = $request->request->get('face_data');
                
                if (empty($faceData)) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Veuillez capturer une image'
                    ], 400);
                }

                $matchingUsers = $faceAuthService->findMatchingUsers($faceData);
                
                if (empty($matchingUsers)) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Aucun visage reconnu. Veuillez réessayer.'
                    ], 404);
                }
                
                if (count($matchingUsers) === 1) {
                    $user = $matchingUsers[0];
                    
                    // Créer et enregistrer le token de sécurité
                    $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
                    $this->container->get('security.token_storage')->setToken($token);
                    $request->getSession()->set('_security_main', serialize($token));
                
                    // Optionnel : Logger une info utile
                    $logger->info('Utilisateur connecté automatiquement via reconnaissance faciale : '.$user->getEmail());
                
                    return $this->json([
                        'success' => true,
                        'redirect' => $this->generateUrl($this->getRedirectRoute($user))
                    ]);
                }
                
                
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
                
            } catch (\Exception $e) {
                $logger->error('Face login error: '.$e->getMessage());
                return $this->json([
                    'success' => false,
                    'message' => 'Erreur de reconnaissance faciale'
                ], 500);
            }
        }
        
        return $this->render('login/face_login.html.twig');
    }

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

    #[Route('/login/face/select', name: 'app_face_login_select')]
    public function selectUser(Request $request, EntityManagerInterface $em, LoginSuccessHandler $loginSuccessHandler): Response
    {
        $userId = $request->request->get('user_id');
        $user = $em->getRepository(Utilisateur::class)->find($userId);
        
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Utilisateur non trouvé'], 404);
        }

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);
        $request->getSession()->set('_security_main', serialize($token)); // << ajoute cette ligne

        $response = $loginSuccessHandler->onAuthenticationSuccess($request, $token);

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
    #[Route('/reset-password/request', name: 'app_reset_password_request', methods: ['POST'])]
public function requestResetPassword(
    Request $request,
    PasswordResetService $resetService,
    EntityManagerInterface $em
): JsonResponse {
    $email = $request->request->get('email');
    $result = $resetService->requestReset($email);
    
    // Log pour débogage
    if ($result['success']) {
        $user = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
        error_log("Code généré: " . $user->getResetPasswordCode());
        error_log("Expiration: " . $user->getResetPasswordCodeExpiresAt()->format('Y-m-d H:i:s'));
    }
    
    return $this->json($result);
}
#[Route('/reset-password/verify', name: 'app_reset_password_verify', methods: ['POST'])]
public function verifyResetCode(
    Request $request,
    PasswordResetService $resetService,
    EntityManagerInterface $em,
    LoggerInterface $logger
): JsonResponse {
    $email = $request->request->get('email');
    $code = $request->request->get('code');
    
    $user = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
    
    if (!$user) {
        $logger->error('Utilisateur non trouvé pour email: {email}', ['email' => $email]);
        return $this->json(['success' => false, 'message' => 'Utilisateur non trouvé']);
    }
    
    // Debug complet
    $logger->info('VERIFICATION - Code saisi: {code}, Code en base: {db_code}, Expiration: {expires}', [
        'code' => $code,
        'db_code' => $user->getResetPasswordCode(),
        'expires' => $user->getResetPasswordCodeExpiresAt() ? 
                    $user->getResetPasswordCodeExpiresAt()->format('Y-m-d H:i:s') : 'null'
    ]);
    
    $result = $resetService->verifyCode($email, $code);
    
    return $this->json($result);
}
#[Route('/reset-password/update', name: 'app_reset_password_update', methods: ['POST'])]
public function updatePassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
{
    $email = $request->request->get('email');
    $code = $request->request->get('code');
    $newPassword = $request->request->get('newPassword');
    $user = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

    if (!$user) {
        return $this->json(['success' => false, 'message' => 'Utilisateur non trouvé']);
    }

    // Vérifier à nouveau le code avant de mettre à jour
    if ($user->getResetPasswordCode() !== $code || $user->getResetPasswordCodeExpiresAt() < new \DateTime()) {
        return $this->json(['success' => false, 'message' => 'Code invalide ou expiré']);
    }

    // Hasher et mettre à jour le mot de passe
    $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
    $user->setPassword($hashedPassword);

    // Réinitialiser les champs de réinitialisation
    $user->setResetPasswordCode(null);
    $user->setResetPasswordAttempts(0);
    $user->setResetPasswordCodeSentAt(null);
    $user->setResetPasswordCodeExpiresAt(null);

    $em->flush();

    return $this->json(['success' => true]);
}
#[Route('/reset-password/resend', name: 'app_reset_password_resend', methods: ['POST'])]
public function resendResetCode(
    Request $request,
    PasswordResetService $resetService
): JsonResponse {
    $result = $resetService->resendCode($request->request->get('email'));
    
    return $this->json($result);
}

#[Route('/reset-password', name: 'app_reset_password_page', methods: ['GET'])]
public function resetPasswordPage(): Response
{
    return $this->render('emails/reset_password.html.twig');
}
}