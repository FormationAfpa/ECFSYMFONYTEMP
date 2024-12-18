<?php

namespace App\Controller;

use App\Repository\RoomReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/reservations')]
class UserReservationController extends AbstractController
{
    #[Route('/', name: 'app_user_reservations')]
    public function index(RoomReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $currentReservations = $reservationRepository->findCurrentReservations($user);
        $pastReservations = $reservationRepository->findPastReservations($user);

        return $this->render('user/reservations.html.twig', [
            'currentReservations' => $currentReservations,
            'pastReservations' => $pastReservations,
        ]);
    }

    #[Route('/cancel/{id}', name: 'app_user_reservation_cancel', methods: ['POST'])]
    public function cancel(RoomReservation $reservation): Response
    {
        $user = $this->getUser();
        if (!$user || $reservation->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit d\'annuler cette réservation.');
        }

        // Vérifier si la réservation n'a pas déjà commencé
        if ($reservation->getStartTime() <= new \DateTime()) {
            $this->addFlash('error', 'Vous ne pouvez pas annuler une réservation qui a déjà commencé.');
            return $this->redirectToRoute('app_user_reservations');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été annulée avec succès.');
        return $this->redirectToRoute('app_user_reservations');
    }
}
