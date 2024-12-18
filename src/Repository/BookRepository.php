<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return Book[] Returns an array of available books
     */
    public function findAvailableBooks(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.isAvailable = :val')
            ->setParameter('val', true)
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Book[] Returns an array of books with their current loan if not available
     */
    public function findBooksWithCurrentLoan(): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.bookLoans', 'bl')
            ->andWhere('bl.returnDate IS NULL')
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Book[] Returns an array of Book objects that are currently overdue
     */
    public function findOverdueBooks(): array
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.bookLoans', 'bl')
            ->where('bl.returnDate IS NULL')
            ->andWhere('bl.dueDate < :today')
            ->setParameter('today', new \DateTime())
            ->orderBy('bl.dueDate', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
