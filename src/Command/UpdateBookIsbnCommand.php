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
    name: 'app:update-book-isbn',
    description: 'Met à jour les ISBN des livres avec des valeurs réelles',
)]
class UpdateBookIsbnCommand extends Command
{
    private array $isbnData = [
        'Le Petit Prince' => '978-2070612758',
        '1984' => '978-2070368228',
        'Les Misérables' => '978-2253096344',
        'L\'Odyssée' => '978-2081427389',
        'Guerre et Paix' => '978-2253089728',
        'Don Quichotte' => '978-2253093435',
        'L\'Étranger' => '978-2070360024',
        'Le Rouge et le Noir' => '978-2253076162',
        'Madame Bovary' => '978-2253004868',
        'Les Fleurs du Mal' => '978-2253007104',
        'Notre-Dame de Paris' => '978-2253096337',
        'Germinal' => '978-2253004226'
    ];

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
            if (isset($this->isbnData[$book->getTitle()])) {
                $book->setIsbn($this->isbnData[$book->getTitle()]);
                $updatedCount++;
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('%d livres ont été mis à jour avec leurs ISBN.', $updatedCount));

        return Command::SUCCESS;
    }
}
