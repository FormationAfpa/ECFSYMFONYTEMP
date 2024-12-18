<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    public const CONDITION_EXCELLENT = 'Excellent état';
    public const CONDITION_GOOD = 'Bon état';
    public const CONDITION_FAIR = 'État moyen';
    public const CONDITION_POOR = 'Mauvais état';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire')]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'auteur est obligatoire')]
    private ?string $author = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: 'L\'année de publication est obligatoire')]
    #[Assert\Range(
        min: -3000,
        max: 2024,
        notInRangeMessage: 'L\'année doit être comprise entre {{ min }} et {{ max }}'
    )]
    private ?int $publicationYear = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le résumé est obligatoire')]
    private ?string $summary = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(name: '`condition_state`', length: 50)]
    #[Assert\NotBlank(message: 'L\'état du livre est obligatoire')]
    #[Assert\Choice(
        choices: [
            self::CONDITION_EXCELLENT,
            self::CONDITION_GOOD,
            self::CONDITION_FAIR,
            self::CONDITION_POOR,
        ],
        message: 'Veuillez sélectionner un état valide'
    )]
    private ?string $condition = null;

    #[ORM\Column]
    private ?bool $isAvailable = true;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: '/^(?:\d{13}|(?:97[89][- ]\d{1,5}[- ]\d+[- ]\d+[- ]\d))$/',
        message: 'L\'ISBN doit être un ISBN-13 valide (13 chiffres commençant par 978 ou 979)'
    )]
    private ?string $isbn = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le stock est obligatoire')]
    #[Assert\Range(
        min: 1,
        max: 100,
        notInRangeMessage: 'Le stock doit être compris entre {{ min }} et {{ max }} exemplaires'
    )]
    private ?int $stock = 1;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'La catégorie est obligatoire')]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookLoan::class, orphanRemoval: true)]
    private Collection $bookLoans;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookComment::class, orphanRemoval: true)]
    private Collection $bookComments;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->bookLoans = new ArrayCollection();
        $this->bookComments = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getPublicationYear(): ?int
    {
        return $this->publicationYear;
    }

    public function setPublicationYear(int $publicationYear): static
    {
        $this->publicationYear = $publicationYear;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function setCondition(string $condition): static
    {
        $this->condition = $condition;

        return $this;
    }

    public function isIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): static
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Collection<int, BookLoan>
     */
    public function getBookLoans(): Collection
    {
        return $this->bookLoans;
    }

    public function addBookLoan(BookLoan $bookLoan): static
    {
        if (!$this->bookLoans->contains($bookLoan)) {
            $this->bookLoans->add($bookLoan);
            $bookLoan->setBook($this);
        }

        return $this;
    }

    public function removeBookLoan(BookLoan $bookLoan): static
    {
        if ($this->bookLoans->removeElement($bookLoan)) {
            // set the owning side to null (unless already changed)
            if ($bookLoan->getBook() === $this) {
                $bookLoan->setBook(null);
            }
        }

        return $this;
    }

    public function isAvailable(): bool
    {
        foreach ($this->bookLoans as $loan) {
            if ($loan->getReturnDate() === null) {
                return false;
            }
        }
        return true;
    }

    public function getCurrentLoan(): ?BookLoan
    {
        foreach ($this->bookLoans as $loan) {
            if ($loan->getReturnDate() === null) {
                return $loan;
            }
        }
        return null;
    }

    /**
     * @return Collection<int, BookComment>
     */
    public function getBookComments(): Collection
    {
        return $this->bookComments;
    }

    public function addBookComment(BookComment $bookComment): static
    {
        if (!$this->bookComments->contains($bookComment)) {
            $this->bookComments->add($bookComment);
            $bookComment->setBook($this);
        }

        return $this;
    }

    public function removeBookComment(BookComment $bookComment): static
    {
        if ($this->bookComments->removeElement($bookComment)) {
            // set the owning side to null (unless already changed)
            if ($bookComment->getBook() === $this) {
                $bookComment->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBook($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBook() === $this) {
                $comment->setBook(null);
            }
        }

        return $this;
    }

    /**
     * Get the average rating of the book
     */
    public function getAverageRating(): ?float
    {
        if ($this->bookComments->isEmpty()) {
            return null;
        }

        $total = 0;
        foreach ($this->bookComments as $comment) {
            $total += $comment->getRating();
        }

        return $total / $this->bookComments->count();
    }
}
