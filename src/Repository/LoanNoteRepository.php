<?php

namespace App\Repository;

use App\Entity\LoanNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LoanNote>
 *
 * @method LoanNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoanNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoanNote[]    findAll()
 * @method LoanNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoanNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoanNote::class);
    }
}
