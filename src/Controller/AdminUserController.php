<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    #[Route('', name: 'admin_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/toggle-ban', name: 'admin_user_toggle_ban', methods: ['POST'])]
    public function toggleBan(User $user, EntityManagerInterface $entityManager): Response
    {
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas vous bannir vous-même.');
            return $this->redirectToRoute('admin_user_index');
        }

        $user->setIsBanned(!$user->isIsBanned());
        $entityManager->flush();

        $status = $user->isIsBanned() ? 'banni' : 'débanni';
        $this->addFlash('success', "L'utilisateur a été $status avec succès.");

        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}/toggle-role', name: 'admin_user_toggle_role', methods: ['POST'])]
    public function toggleRole(User $user, EntityManagerInterface $entityManager): Response
    {
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier votre propre rôle.');
            return $this->redirectToRoute('admin_user_index');
        }

        $currentRoles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $currentRoles)) {
            $user->setRoles(['ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        }

        $entityManager->flush();
        $this->addFlash('success', 'Le rôle de l\'utilisateur a été modifié avec succès.');

        return $this->redirectToRoute('admin_user_index');
    }
}
