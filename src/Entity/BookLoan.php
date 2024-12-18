<?php

namespace App\Entity;

use App\Repository\BookLoanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookLoanRepository::class)]
class BookLoan
{
    public const LOAN_DURATION_DAYS = 6;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookLoans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'bookLoans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $loanDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $returnDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adminComment = null;

    #[ORM\Column]
    private ?bool $isExtended = false;

    #[ORM\OneToMany(mappedBy: 'bookLoan', targetEntity: LoanNote::class, orphanRemoval: true)]
    private Collection $loanNotes;

    public function __construct()
    {
        $this->loanDate = new \DateTime();
        $this->dueDate = (new \DateTime())->modify('+' . self::LOAN_DURATION_DAYS . ' days');
        $this->loanNotes = new ArrayCollection();
    }

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

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;
        return $this;
    }

    public function getLoanDate(): ?\DateTimeInterface
    {
        return $this->loanDate;
    }

    public function setLoanDate(\DateTimeInterface $loanDate): static
    {
        $this->loanDate = $loanDate;
        // Met à jour la date de retour prévue
        $this->dueDate = (clone $loanDate)->modify('+' . self::LOAN_DURATION_DAYS . ' days');
        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): static
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getReturnDate(): ?\DateTimeInterface
    {
        return $this->returnDate;
    }

    public function setReturnDate(?\DateTimeInterface $returnDate): static
    {
        $this->returnDate = $returnDate;
        return $this;
    }

    public function getAdminComment(): ?string
    {
        return $this->adminComment;
    }

    public function setAdminComment(?string $adminComment): static
    {
        $this->adminComment = $adminComment;
        return $this;
    }

    public function isIsExtended(): ?bool
    {
        return $this->isExtended;
    }

    public function setIsExtended(bool $isExtended): static
    {
        $this->isExtended = $isExtended;
        if ($isExtended) {
            // Ajoute 6 jours supplémentaires à la date de retour prévue
            $this->dueDate = (clone $this->dueDate)->modify('+' . self::LOAN_DURATION_DAYS . ' days');
        }
        return $this;
    }

    /**
     * @return Collection<int, LoanNote>
     */
    public function getLoanNotes(): Collection
    {
        return $this->loanNotes;
    }

    public function addLoanNote(LoanNote $loanNote): static
    {
        if (!$this->loanNotes->contains($loanNote)) {
            $this->loanNotes->add($loanNote);
            $loanNote->setBookLoan($this);
        }

        return $this;
    }

    public function removeLoanNote(LoanNote $loanNote): static
    {
        if ($this->loanNotes->removeElement($loanNote)) {
            // set the owning side to null (unless already changed)
            if ($loanNote->getBookLoan() === $this) {
                $loanNote->setBookLoan(null);
            }
        }

        return $this;
    }

    /**
     * Vérifie si le prêt est en retard
     * @return bool
     */
    public function isOverdue(): bool
    {
        if ($this->returnDate !== null) {
            return false;
        }
        return $this->dueDate < new \DateTime();
    }

    /**
     * Prolonge le prêt de 6 jours supplémentaires
     * @throws \Exception si le prêt a déjà été prolongé ou est en retard
     */
    public function extend(): void
    {
        if ($this->isExtended) {
            throw new \Exception('Ce prêt a déjà été prolongé une fois.');
        }

        if ($this->isOverdue()) {
            throw new \Exception('Impossible de prolonger un prêt en retard.');
        }

        $this->dueDate = (clone $this->dueDate)->modify('+' . self::LOAN_DURATION_DAYS . ' days');
        $this->isExtended = true;
    }
}
