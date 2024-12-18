<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 *
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    /**
     * Trouve les abonnements actifs
     */
    public function findActive(): array
    {
        $now = new \DateTime();
        
        return $this->createQueryBuilder('s')
            ->andWhere('s.endDate > :now')
            ->setParameter('now', $now)
            ->orderBy('s.endDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les abonnements expirés
     */
    public function findExpired(): array
    {
        $now = new \DateTime();
        
        return $this->createQueryBuilder('s')
            ->andWhere('s.endDate <= :now')
            ->setParameter('now', $now)
            ->orderBy('s.endDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve l'abonnement actif d'un utilisateur
     */
    public function findActiveByUser($user): ?Subscription
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('s.endDate > :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
