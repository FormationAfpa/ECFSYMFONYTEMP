<?php

namespace App\Entity;

use App\Repository\RoomReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoomReservationRepository::class)]
class RoomReservation
{
    public const MIN_DURATION_HOURS = 1;
    public const MAX_DURATION_HOURS = 4;
    public const OPENING_HOUR = 8;
    public const CLOSING_HOUR = 19;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'roomReservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'roomReservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'L\'heure de début est obligatoire')]
    #[Assert\GreaterThanOrEqual(
        'today 08:00',
        message: 'La réservation doit commencer au plus tôt à 8h'
    )]
    #[Assert\LessThanOrEqual(
        'today 19:00',
        message: 'La réservation doit se terminer au plus tard à 19h'
    )]
    #[Assert\Expression(
        "this.isWeekday(value)",
        message: "Les réservations ne sont possibles que du lundi au vendredi"
    )]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'L\'heure de fin est obligatoire')]
    #[Assert\GreaterThan(
        propertyPath: 'startTime',
        message: 'L\'heure de fin doit être après l\'heure de début'
    )]
    #[Assert\LessThanOrEqual(
        'today 19:00',
        message: 'La réservation doit se terminer au plus tard à 19h'
    )]
    #[Assert\Expression(
        "this.getEndTime() > this.getStartTime()",
        message: "La date de fin doit être après la date de début"
    )]
    #[Assert\Expression(
        "this.getDurationInHours() <= this.getMaxDurationHours()",
        message: "La durée de réservation ne peut pas dépasser {{ value }} heures"
    )]
    #[Assert\Callback(['App\Validator\RoomReservationValidator', 'validateDuration'])]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le motif de la réservation est obligatoire')]
    private ?string $purpose = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;
        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    public function setPurpose(string $purpose): static
    {
        $this->purpose = $purpose;
        return $this;
    }

    public function getDurationInHours(): float
    {
        if (!$this->startTime || !$this->endTime) {
            return 0;
        }
        
        $interval = $this->endTime->diff($this->startTime);
        return $interval->h + ($interval->i / 60);
    }

    public function getMaxDurationHours(): int
    {
        return self::MAX_DURATION_HOURS;
    }

    public function isWeekday(\DateTimeInterface $date): bool
    {
        $dayOfWeek = (int) $date->format('N');
        return $dayOfWeek >= 1 && $dayOfWeek <= 5;
    }
}
