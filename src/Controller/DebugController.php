<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DebugController extends AbstractController
{
    #[Route('/debug/roles', name: 'app_debug_roles')]
    public function roles(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new Response('Not logged in');
        }
        
        $roles = $user->getRoles();
        $role = method_exists($user, 'getRole') ? $user->getRole() : 'N/A';
        
        return new Response(sprintf(
            'User: %s<br>Role: %s<br>Roles: %s<br>Is Admin: %s',
            $user->getUserIdentifier(),
            $role,
            implode(', ', $roles),
            $this->isGranted('ROLE_ADMIN') ? 'Yes' : 'No'
        ));
    }
}
