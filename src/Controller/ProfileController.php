<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\BookLoanRepository;
use App\Repository\RoomReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/profile', requirements: ['_locale' => 'en|fr'], defaults: ['_locale' => 'fr'])]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager,
        BookLoanRepository $bookLoanRepository,
        RoomReservationRepository $roomReservationRepository
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Vos informations ont été mises à jour avec succès !');
            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        // Récupérer tous les emprunts (en cours et passés)
        $currentLoans = $bookLoanRepository->findCurrentLoans($user);
        $pastLoans = $bookLoanRepository->findPastLoans($user);

        // Récupérer toutes les réservations (en cours et passées)
        $currentReservations = $roomReservationRepository->findCurrentReservations($user);
        $pastReservations = $roomReservationRepository->findPastReservations($user);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'currentLoans' => $currentLoans,
            'pastLoans' => $pastLoans,
            'currentReservations' => $currentReservations,
            'pastReservations' => $pastReservations,
        ]);
    }

    #[Route('/loans', name: 'app_profile_loans')]
    public function loans(BookLoanRepository $bookLoanRepository): Response
    {
        $user = $this->getUser();
        $currentLoans = $bookLoanRepository->findCurrentLoans($user);
        $pastLoans = $bookLoanRepository->findPastLoans($user);

        return $this->render('profile/loans.html.twig', [
            'currentLoans' => $currentLoans,
            'pastLoans' => $pastLoans,
        ]);
    }

    #[Route('/reservations', name: 'app_profile_reservations')]
    public function reservations(RoomReservationRepository $roomReservationRepository): Response
    {
        $user = $this->getUser();
        $currentReservations = $roomReservationRepository->findCurrentReservations($user);
        $pastReservations = $roomReservationRepository->findPastReservations($user);

        return $this->render('profile/reservations.html.twig', [
            'currentReservations' => $currentReservations,
            'pastReservations' => $pastReservations,
        ]);
    }
}
