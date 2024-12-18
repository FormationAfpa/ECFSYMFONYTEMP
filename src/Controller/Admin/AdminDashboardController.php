<?php

namespace App\Controller\Admin;

use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(
        BookRepository $bookRepository,
        UserRepository $userRepository,
        RoomRepository $roomRepository
    ): Response {
        // Récupérer les statistiques
        $overdueBooks = $bookRepository->findOverdueBooks();
        $totalUsers = $userRepository->count([]);
        $totalRooms = $roomRepository->count([]);
        $totalBooks = $bookRepository->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'overdueBooks' => $overdueBooks,
            'totalUsers' => $totalUsers,
            'totalRooms' => $totalRooms,
            'totalBooks' => $totalBooks,
        ]);
    }
}
