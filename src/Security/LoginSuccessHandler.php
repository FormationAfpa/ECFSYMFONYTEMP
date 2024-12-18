<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        // Récupérer l'utilisateur
        $user = $token->getUser();
        
        // Vérifier si l'utilisateur est un admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            // Rediriger vers le tableau de bord admin
            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }
        
        // Rediriger vers la page d'accueil pour les utilisateurs normaux
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}
