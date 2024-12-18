<?php

namespace App\Repository;

use App\Entity\BookLoan;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookLoan>
 *
 * @method BookLoan|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookLoan|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookLoan[]    findAll()
 * @method BookLoan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookLoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookLoan::class);
    }

    /**
     * @return BookLoan[] Returns an array of current BookLoan objects
     */
    public function findCurrentLoans(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.user = :user')
            ->andWhere('b.returnDate IS NULL')
            ->setParameter('user', $user)
            ->orderBy('b.loanDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return BookLoan[] Returns an array of past BookLoan objects
     */
    public function findPastLoans(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.user = :user')
            ->andWhere('b.returnDate IS NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('b.returnDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
