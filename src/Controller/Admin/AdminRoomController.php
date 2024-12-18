<?php

namespace App\Controller\Admin;

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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/rooms')]
#[IsGranted('ROLE_ADMIN')]
class AdminRoomController extends AbstractController
{
    #[Route('/', name: 'admin_room_index', methods: ['GET'])]
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('admin/room/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    #[Route('/show/{id}', name: 'admin_room_show', methods: ['GET'])]
    public function show(Room $room): Response
    {
        return $this->render('admin/room/show.html.twig', [
            'room' => $room,
            'reservations' => $room->getRoomReservations(),
        ]);
    }

    #[Route('/calendar/{id}', name: 'admin_room_calendar', methods: ['GET'])]
    public function calendar(Room $room): Response
    {
        return $this->render('admin/room/calendar.html.twig', [
            'room' => $room,
            'reservations' => $room->getRoomReservations(),
        ]);
    }

    #[Route('/new', name: 'admin_room_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();

            $this->addFlash('success', 'La salle a été créée avec succès.');
            return $this->redirectToRoute('admin_room_index');
        }

        return $this->render('admin/room/form.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
            'title' => 'Nouvelle salle',
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_room_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La salle a été modifiée avec succès.');
            return $this->redirectToRoute('admin_room_show', ['id' => $room->getId()]);
        }

        return $this->render('admin/room/form.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
            'title' => 'Modifier la salle',
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_room_delete', methods: ['POST'])]
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager->remove($room);
            $entityManager->flush();
            $this->addFlash('success', 'La salle a été supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_room_index');
    }

    #[Route('/reserve/{id}', name: 'admin_room_reserve', methods: ['GET', 'POST'])]
    public function reserve(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $reservation = new RoomReservation();
        $reservation->setRoom($room);
        $reservation->setUser($this->getUser());

        $form = $this->createForm(RoomReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été créée avec succès.');
            return $this->redirectToRoute('admin_room_calendar', ['id' => $room->getId()]);
        }

        return $this->render('admin/room/reserve.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }
}
