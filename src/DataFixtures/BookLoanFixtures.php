<?php

namespace App\DataFixtures;

use App\Entity\BookLoan;
use App\Entity\User;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookLoanFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $users = [
            $this->getReference('user_1', User::class),
            $this->getReference('user_2', User::class)
        ];
        
        $books = [
            $this->getReference('book_le_petit_prince', Book::class),
            $this->getReference('book_1984', Book::class),
            $this->getReference('book_fahrenheit_451', Book::class)
        ];
        
        // Créer quelques emprunts
        foreach ($books as $index => $book) {
            // Premier emprunt (terminé)
            $loan1 = new BookLoan();
            $loan1->setBook($book);
            $loan1->setUser($users[0]);
            
            // Emprunt terminé (il y a 20 jours)
            $loanDate1 = new \DateTime('2024-11-28'); // Il y a 20 jours
            $dueDate1 = clone $loanDate1;
            $dueDate1->modify('+6 days');
            $returnDate1 = clone $dueDate1; // Le livre a été retourné à la date prévue
            
            $loan1->setLoanDate($loanDate1);
            $loan1->setDueDate($dueDate1);
            $loan1->setReturnDate($returnDate1);
            $manager->persist($loan1);
            $this->addReference(sprintf('book-loan-%d', $index * 2), $loan1);

            // Deuxième emprunt (en cours)
            $loan2 = new BookLoan();
            $loan2->setBook($book);
            $loan2->setUser($users[1]);
            
            // Emprunt en cours (il y a 3 jours)
            $loanDate2 = new \DateTime('2024-12-15'); // Il y a 3 jours
            $dueDate2 = clone $loanDate2;
            $dueDate2->modify('+6 days');
            
            $loan2->setLoanDate($loanDate2);
            $loan2->setDueDate($dueDate2);
            $loan2->setReturnDate(null); // Pas encore retourné
            $manager->persist($loan2);
            $this->addReference(sprintf('book-loan-%d', $index * 2 + 1), $loan2);
        }

        // Ajout d'un emprunt en retard
        $lateBook = $books[0]; // Le Petit Prince
        $lateLoan = new BookLoan();
        $lateLoan->setBook($lateBook);
        $lateLoan->setUser($users[0]);
        
        // Emprunt en retard (il y a 10 jours)
        $lateLoanDate = new \DateTime('2024-12-08'); // Il y a 10 jours
        $lateDueDate = clone $lateLoanDate;
        $lateDueDate->modify('+6 days');
        
        $lateLoan->setLoanDate($lateLoanDate);
        $lateLoan->setDueDate($lateDueDate);
        $lateLoan->setReturnDate(null); // Pas encore retourné
        $manager->persist($lateLoan);
        $this->addReference('book-loan-late', $lateLoan);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            BookFixtures::class,
        ];
    }
}
