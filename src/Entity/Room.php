<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?bool $hasWifi = false;

    #[ORM\Column]
    private ?bool $hasProjector = false;

    #[ORM\Column]
    private ?bool $hasWhiteboard = false;

    #[ORM\Column]
    private ?bool $hasPowerOutlets = false;

    #[ORM\Column]
    private ?bool $hasTV = false;

    #[ORM\Column]
    private ?bool $hasAirConditioning = false;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: RoomReservation::class)]
    private Collection $roomReservations;

    public function __construct()
    {
        $this->roomReservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function isHasWifi(): ?bool
    {
        return $this->hasWifi;
    }

    public function setHasWifi(bool $hasWifi): static
    {
        $this->hasWifi = $hasWifi;

        return $this;
    }

    public function isHasProjector(): ?bool
    {
        return $this->hasProjector;
    }

    public function setHasProjector(bool $hasProjector): static
    {
        $this->hasProjector = $hasProjector;

        return $this;
    }

    public function isHasWhiteboard(): ?bool
    {
        return $this->hasWhiteboard;
    }

    public function setHasWhiteboard(bool $hasWhiteboard): static
    {
        $this->hasWhiteboard = $hasWhiteboard;

        return $this;
    }

    public function isHasPowerOutlets(): ?bool
    {
        return $this->hasPowerOutlets;
    }

    public function setHasPowerOutlets(bool $hasPowerOutlets): static
    {
        $this->hasPowerOutlets = $hasPowerOutlets;

        return $this;
    }

    public function isHasTV(): ?bool
    {
        return $this->hasTV;
    }

    public function setHasTV(bool $hasTV): static
    {
        $this->hasTV = $hasTV;

        return $this;
    }

    public function isHasAirConditioning(): ?bool
    {
        return $this->hasAirConditioning;
    }

    public function setHasAirConditioning(bool $hasAirConditioning): static
    {
        $this->hasAirConditioning = $hasAirConditioning;

        return $this;
    }

    /**
     * @return Collection<int, RoomReservation>
     */
    public function getRoomReservations(): Collection
    {
        return $this->roomReservations;
    }

    public function addRoomReservation(RoomReservation $roomReservation): static
    {
        if (!$this->roomReservations->contains($roomReservation)) {
            $this->roomReservations->add($roomReservation);
            $roomReservation->setRoom($this);
        }

        return $this;
    }

    public function removeRoomReservation(RoomReservation $roomReservation): static
    {
        if ($this->roomReservations->removeElement($roomReservation)) {
            // set the owning side to null (unless already changed)
            if ($roomReservation->getRoom() === $this) {
                $roomReservation->setRoom(null);
            }
        }

        return $this;
    }
}
