<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/toggle-active', name: 'admin_user_toggle_active', methods: ['POST'])]
    public function toggleActive(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsActive(!$user->isIsActive());
        $entityManager->flush();

        $this->addFlash('success', sprintf(
            'L\'utilisateur %s a été %s',
            $user->getEmail(),
            $user->isIsActive() ? 'activé' : 'désactivé'
        ));

        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}/toggle-ban', name: 'admin_user_toggle_ban', methods: ['POST'])]
    public function toggleBan(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsBanned(!$user->isIsBanned());
        $entityManager->flush();

        $this->addFlash('success', sprintf(
            'L\'utilisateur %s a été %s',
            $user->getEmail(),
            $user->isIsBanned() ? 'banni' : 'débanni'
        ));

        return $this->redirectToRoute('admin_user_index');
    }
}
