<?php

namespace App\Twig;

use App\Entity\Room;
use App\Repository\RoomReservationRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoomExtension extends AbstractExtension
{
    private $reservationRepository;

    public function __construct(RoomReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('isTimeSlotReserved', [$this, 'isTimeSlotReserved']),
        ];
    }

    public function isTimeSlotReserved(Room $room, int $dayOfWeek, int $hour): bool
    {
        $now = new \DateTime();
        $date = clone $now;
        
        // Ajuster à la semaine courante
        $currentDayOfWeek = (int)$date->format('N');
        $daysToAdd = $dayOfWeek - $currentDayOfWeek;
        if ($daysToAdd < 0) {
            $daysToAdd += 7;
        }
        $date->modify("+{$daysToAdd} days");
        
        // Définir l'heure de début et de fin
        $startTime = clone $date;
        $startTime->setTime($hour, 0);
        
        $endTime = clone $startTime;
        $endTime->setTime($hour + 1, 0);

        // Vérifier s'il existe une réservation pour ce créneau
        return $this->reservationRepository->findConflictingReservation($room, $startTime, $endTime) !== null;
    }
}
