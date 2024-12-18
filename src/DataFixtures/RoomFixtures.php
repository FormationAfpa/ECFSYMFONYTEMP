<?php

namespace App\DataFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoomFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $rooms = [
            [
                'name' => 'Salle de réunion A',
                'capacity' => 8,
                'hasWifi' => true,
                'hasProjector' => true,
                'hasWhiteboard' => true,
                'hasPowerOutlets' => true,
                'hasTV' => false,
                'hasAirConditioning' => true,
            ],
            [
                'name' => 'Salle de travail B',
                'capacity' => 4,
                'hasWifi' => true,
                'hasProjector' => false,
                'hasWhiteboard' => true,
                'hasPowerOutlets' => true,
                'hasTV' => false,
                'hasAirConditioning' => true,
            ],
            [
                'name' => 'Salle multimédia C',
                'capacity' => 6,
                'hasWifi' => true,
                'hasProjector' => true,
                'hasWhiteboard' => false,
                'hasPowerOutlets' => true,
                'hasTV' => true,
                'hasAirConditioning' => true,
            ],
            [
                'name' => 'Espace collaboratif D',
                'capacity' => 10,
                'hasWifi' => true,
                'hasProjector' => true,
                'hasWhiteboard' => true,
                'hasPowerOutlets' => true,
                'hasTV' => true,
                'hasAirConditioning' => true,
            ],
            [
                'name' => 'Salle de travail E',
                'capacity' => 4,
                'hasWifi' => true,
                'hasProjector' => false,
                'hasWhiteboard' => true,
                'hasPowerOutlets' => true,
                'hasTV' => false,
                'hasAirConditioning' => true,
            ],
        ];

        foreach ($rooms as $roomData) {
            $room = new Room();
            $room->setName($roomData['name']);
            $room->setCapacity($roomData['capacity']);
            $room->setHasWifi($roomData['hasWifi']);
            $room->setHasProjector($roomData['hasProjector']);
            $room->setHasWhiteboard($roomData['hasWhiteboard']);
            $room->setHasPowerOutlets($roomData['hasPowerOutlets']);
            $room->setHasTV($roomData['hasTV']);
            $room->setHasAirConditioning($roomData['hasAirConditioning']);

            $manager->persist($room);
        }

        $manager->flush();
    }
}
