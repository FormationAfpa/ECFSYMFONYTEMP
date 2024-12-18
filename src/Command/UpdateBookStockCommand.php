<?php

namespace App\Command;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-book-stock',
    description: 'Met à jour le stock de tous les livres pour avoir au moins 1 exemplaire',
)]
class UpdateBookStockCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $books = $this->entityManager->getRepository(Book::class)->findAll();
        $updatedCount = 0;

        foreach ($books as $book) {
            if ($book->getStock() < 1) {
                $book->setStock(1);
                $updatedCount++;
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('%d livres ont été mis à jour avec un stock minimum de 1 exemplaire.', $updatedCount));

        return Command::SUCCESS;
    }
}
