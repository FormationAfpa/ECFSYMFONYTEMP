<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    public const MONTHLY_PRICE = 23.99;
    public const ANNUAL_DISCOUNT = 0.10; // 10% discount

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null; // 'mensuel' or 'annuel'

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\OneToOne(inversedBy: 'subscription', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        $this->calculatePrice();
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    private function calculatePrice(): void
    {
        if ($this->type === 'mensuel') {
            $this->price = (string)self::MONTHLY_PRICE;
        } else {
            $annualPrice = self::MONTHLY_PRICE * 12;
            $discount = $annualPrice * self::ANNUAL_DISCOUNT;
            $this->price = (string)($annualPrice - $discount);
        }
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        $this->calculateEndDate();
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    private function calculateEndDate(): void
    {
        if ($this->startDate) {
            $endDate = clone $this->startDate;
            if ($this->type === 'mensuel') {
                $endDate->modify('+1 month');
            } else {
                $endDate->modify('+1 year');
            }
            $this->endDate = $endDate;
        }
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
}
