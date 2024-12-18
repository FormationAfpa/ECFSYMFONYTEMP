<?php

namespace App\Validator;

use App\Entity\RoomReservation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use \DateTime;

class RoomReservationValidator
{
    public static function validateDuration(\DateTime $value, ExecutionContextInterface $context): void
    {
        $object = $context->getObject();
        if (!$object instanceof RoomReservation) {
            return;
        }

        $startTime = $object->getStartTime();
        if (!$startTime) {
            return;
        }

        // $value est l'heure de fin
        $endTime = $value;
        
        // Calculer la différence en heures
        $diffInHours = abs($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600;
        
        if ($diffInHours > 4) {
            $context->buildViolation('La durée de réservation ne peut pas dépasser 4 heures')
                ->addViolation();
        }
        
        if ($diffInHours < 1) {
            $context->buildViolation('La durée de réservation doit être d\'au moins 1 heure')
                ->addViolation();
        }
    }
}
