<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\RoomReservation;
use App\Form\RoomReservationType;
use App\Repository\RoomReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/room-reservation')]
#[IsGranted('ROLE_USER')]
class RoomReservationController extends AbstractController
{
    #[Route('/', name: 'app_room_reservation_index', methods: ['GET'])]
    public function index(RoomReservationRepository $roomReservationRepository): Response
    {
        $user = $this->getUser();
        return $this->render('room_reservation/index.html.twig', [
            'room_reservations' => $roomReservationRepository->findBy(['user' => $user]),
        ]);
    }

    #[Route('/room/{id}/new', name: 'app_room_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Room $room, EntityManagerInterface $entityManager, RoomReservationRepository $reservationRepository): Response
    {
        // Vérifier si l'utilisateur a un abonnement actif
        $user = $this->getUser();
        $subscription = $user->getSubscription();
        if (!$subscription || $subscription->getEndDate() < new \DateTime()) {
            $this->addFlash('error', 'Vous devez avoir un abonnement actif pour réserver une salle.');
            return $this->redirectToRoute('app_subscription_new');
        }

        $reservation = new RoomReservation();
        $reservation->setRoom($room);
        $reservation->setUser($user);

        $form = $this->createForm(RoomReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier les conflits de réservation
            $conflictingReservations = $reservationRepository->findConflictingReservations(
                $room,
                $reservation->getStartTime(),
                $reservation->getEndTime()
            );

            if (!empty($conflictingReservations)) {
                $this->addFlash('error', 'Ce créneau n\'est pas disponible.');
                return $this->redirectToRoute('app_room_reservation_new', ['id' => $room->getId()]);
            }

            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réservation a été enregistrée avec succès.');
            return $this->redirectToRoute('app_room_reservation_index');
        }

        return $this->render('room_reservation/new.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_room_reservation_show', methods: ['GET'])]
    public function show(RoomReservation $roomReservation): Response
    {
        // Vérifier que l'utilisateur est propriétaire de la réservation
        if ($roomReservation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette réservation.');
        }

        return $this->render('room_reservation/show.html.twig', [
            'room_reservation' => $roomReservation,
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_room_reservation_cancel', methods: ['POST'])]
    public function cancel(Request $request, RoomReservation $roomReservation, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est propriétaire de la réservation
        if ($roomReservation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette réservation.');
        }

        if ($this->isCsrfTokenValid('cancel'.$roomReservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($roomReservation);
            $entityManager->flush();
            $this->addFlash('success', 'La réservation a été annulée avec succès.');
        }

        return $this->redirectToRoute('app_room_reservation_index');
    }
}
