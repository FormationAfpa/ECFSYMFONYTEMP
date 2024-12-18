<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id}/toggle-ban', name: 'app_admin_toggle_ban', methods: ['POST'])]
    public function toggleBan(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsBanned(!$user->isIsBanned());
        $entityManager->flush();

        $this->addFlash(
            'success',
            $user->isIsBanned() ? 'Utilisateur banni avec succès.' : 'Utilisateur débanni avec succès.'
        );

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/users/{id}/toggle-role', name: 'app_admin_toggle_role', methods: ['POST'])]
    public function toggleRole(User $user, EntityManagerInterface $entityManager): Response
    {
        $newRoles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $newRoles)) {
            $newRoles = ['ROLE_USER'];
        } else {
            $newRoles[] = 'ROLE_ADMIN';
        }
        $user->setRoles($newRoles);
        $entityManager->flush();

        $this->addFlash(
            'success',
            in_array('ROLE_ADMIN', $newRoles) 
                ? 'Droits administrateur accordés avec succès.' 
                : 'Droits administrateur retirés avec succès.'
        );

        return $this->redirectToRoute('app_admin_users');
    }
}
