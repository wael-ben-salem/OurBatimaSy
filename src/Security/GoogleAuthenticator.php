<?php
// src/Security/GoogleAuthenticator.php

namespace App\Security;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GoogleAuthenticator extends AbstractAuthenticator 
{
    private Google $googleProvider;
    private bool $isGoogleProviderInitialized = false;

    public function __construct(
        private string $googleClientId,
        private string $googleClientSecret,
        private string $googleRedirectUri,
        private RouterInterface $router,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private LoginSuccessHandler $loginSuccessHandler,
        private RequestStack $requestStack
    ) {}

    private function initializeGoogleProvider(): void
    {
        if ($this->isGoogleProviderInitialized) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        $baseUrl = $request ? $request->getSchemeAndHttpHost() : '';

        $this->googleProvider = new Google([
            'clientId' => $this->googleClientId,
            'clientSecret' => $this->googleClientSecret,
            'redirectUri' => $baseUrl . $this->router->generate('app_google_check'),
        ]);

        $this->isGoogleProviderInitialized = true;
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        $this->initializeGoogleProvider();
        
        $authUrl = $this->googleProvider->getAuthorizationUrl([
            'scope' => ['email', 'profile'],
        ]);
        
        return new RedirectResponse($authUrl);
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_google_check' && 
               $request->query->has('code');
    }

    public function authenticate(Request $request): Passport
    {
        $this->initializeGoogleProvider();
        
        $code = $request->query->get('code');
        $token = $this->googleProvider->getAccessToken('authorization_code', ['code' => $code]);
        
        /** @var GoogleUser $googleUser */
        $googleUser = $this->googleProvider->getResourceOwner($token);

        $email = $googleUser->getEmail();
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new Utilisateur();
            $user->setEmail($email);
            $user->setNom($googleUser->getLastName() ?? '');
            $user->setPrenom($googleUser->getFirstName() ?? '');
            $user->setPassword($this->passwordHasher->hashPassword($user, bin2hex(random_bytes(16))));
            $user->setRole('Client');
            $user->setStatut('en_attente');
            
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return new SelfValidatingPassport(new UserBadge($email));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var Utilisateur $user */
        $user = $token->getUser();
        
        if (!$user->getAdresse() || !$user->getTelephone()) {
            return new RedirectResponse($this->router->generate('app_complete_profile'));
        }

        return $this->loginSuccessHandler->onAuthenticationSuccess($request, $token);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->router->generate('app_login', [
            'error' => $exception->getMessageKey()
        ]));
    }
}