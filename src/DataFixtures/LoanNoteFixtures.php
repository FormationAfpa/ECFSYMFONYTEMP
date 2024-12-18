<?php

namespace App\DataFixtures;

use App\Entity\LoanNote;
use App\Entity\User;
use App\Entity\BookLoan;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoanNoteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $admin = $this->getReference('admin_user', User::class);
        $bookLoans = [];
        
        // Récupérer tous les emprunts créés
        for ($i = 0; $i < 6; $i++) {
            $loanReference = sprintf('book-loan-%d', $i);
            if ($this->hasReference($loanReference, BookLoan::class)) {
                $bookLoans[] = $this->getReference($loanReference, BookLoan::class);
            }
        }

        $comments = [
            'Le livre a été rendu en bon état.',
            'Quelques pages cornées, mais rien de grave.',
            'L\'emprunteur a pris grand soin du livre.',
            'Retard de 2 jours mais livre en parfait état.',
            'A prévenu du retard, très respectueux.',
            'Première page légèrement abîmée.',
            'Couverture un peu usée.',
        ];

        foreach ($bookLoans as $index => $loan) {
            // Ajouter 1 ou 2 commentaires par emprunt
            $numComments = rand(1, 2);
            for ($i = 0; $i < $numComments; $i++) {
                $note = new LoanNote();
                $note->setContent($comments[array_rand($comments)]);
                $note->setBookLoan($loan);
                $note->setCreatedBy($admin);
                
                // Date aléatoire entre la date d'emprunt et maintenant
                $loanDate = $loan->getLoanDate();
                $now = new \DateTime();
                $interval = $now->getTimestamp() - $loanDate->getTimestamp();
                $randomTimestamp = $loanDate->getTimestamp() + rand(0, $interval);
                $randomDate = new \DateTime();
                $randomDate->setTimestamp($randomTimestamp);
                
                $note->setCreatedAt($randomDate);
                
                $manager->persist($note);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            BookLoanFixtures::class,
        ];
    }
}
