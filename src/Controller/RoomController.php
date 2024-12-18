<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\RoomReservation;
use App\Form\RoomReservationType;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use App\Repository\RoomReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/room')]
class RoomController extends AbstractController
{
    #[Route('/', name: 'app_room_index', methods: ['GET'])]
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('room/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_room_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/new.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_room_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $reservation = new RoomReservation();
        $reservation->setRoom($room);
        
        if ($this->getUser()) {
            $reservation->setUser($this->getUser());
        }

        $form = $this->createForm(RoomReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                $this->addFlash('error', 'Vous devez être connecté pour réserver une salle.');
                return $this->redirectToRoute('app_login');
            }

            if (!$this->getUser()->hasActiveSubscription()) {
                $this->addFlash('warning', 'Vous devez avoir un abonnement actif pour réserver une salle.');
                return $this->redirectToRoute('app_subscription');
            }

            // Vérifier les conflits de réservation
            $conflictingReservations = $entityManager->getRepository(RoomReservation::class)
                ->findConflictingReservations($room, $reservation->getStartTime(), $reservation->getEndTime());

            if (!empty($conflictingReservations)) {
                $this->addFlash('error', 'La salle est déjà réservée sur ce créneau.');
                return $this->redirectToRoute('app_room_show', ['id' => $room->getId()]);
            }

            // Vérifier que la durée ne dépasse pas 4 heures
            $duration = $reservation->getDurationInHours();
            if ($duration > RoomReservation::MAX_DURATION_HOURS) {
                $this->addFlash('error', sprintf('La durée de réservation ne peut pas dépasser %d heures.', RoomReservation::MAX_DURATION_HOURS));
                return $this->redirectToRoute('app_room_show', ['id' => $room->getId()]);
            }

            try {
                $entityManager->persist($reservation);
                $entityManager->flush();

                $this->addFlash('success', sprintf(
                    'Votre réservation pour %s le %s de %s à %s a été confirmée.',
                    $room->getName(),
                    $reservation->getStartTime()->format('d/m/Y'),
                    $reservation->getStartTime()->format('H:i'),
                    $reservation->getEndTime()->format('H:i')
                ));

                return $this->redirectToRoute('app_room_show', ['id' => $room->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la réservation. Veuillez réessayer.');
            }
        }

        return $this->render('room/show.html.twig', [
            'room' => $room,
            'reservationForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_room_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_room_delete', methods: ['POST'])]
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/mes-reservations', name: 'app_user_reservations', methods: ['GET'])]
    public function userReservations(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $reservations = $entityManager->getRepository(RoomReservation::class)
            ->findBy(['user' => $this->getUser()], ['startTime' => 'DESC']);

        return $this->render('room/user_reservations.html.twig', [
            'reservations' => $reservations
        ]);
    }
}
