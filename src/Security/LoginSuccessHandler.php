<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
{
    /** @var UserInterface $user */
    $user = $token->getUser();

    $redirectUrl = $this->router->generate(
        in_array('ROLE_CLIENT', $user->getRoles(), true) ? 'app_welcomeFront' : 'app_welcome'
    );

    // ⚠️ Si c’est une requête AJAX, on renvoie du JSON
    if ($request->isXmlHttpRequest()) {
        return new JsonResponse([
            'success' => true,
            'redirect' => $redirectUrl
        ]);
    }

    // Sinon, redirection classique
    return new RedirectResponse($redirectUrl);
}
}
