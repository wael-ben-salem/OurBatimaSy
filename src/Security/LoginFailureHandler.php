<?php
// src/Security/LoginFailureHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class LoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => false,
                'error' => $exception->getMessageKey(),
            ], 401);
        }
        
        // Pour les requêtes normales, Symfony gère automatiquement la redirection
        throw $exception;
    }
}