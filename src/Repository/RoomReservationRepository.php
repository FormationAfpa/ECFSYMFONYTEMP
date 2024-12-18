<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\RoomReservation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoomReservation>
 *
 * @method RoomReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomReservation[]    findAll()
 * @method RoomReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomReservation::class);
    }

    /**
     * @return RoomReservation[] Returns an array of current RoomReservation objects
     */
    public function findCurrentReservations(User $user): array
    {
        $now = new \DateTime();
        
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.endTime >= :now')
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->orderBy('r.startTime', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return RoomReservation[] Returns an array of past RoomReservation objects
     */
    public function findPastReservations(User $user): array
    {
        $now = new \DateTime();
        
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.endTime < :now')
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->orderBy('r.startTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findConflictingReservation(Room $room, \DateTimeInterface $startTime, \DateTimeInterface $endTime): ?RoomReservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.room = :room')
            ->andWhere('
                (r.startTime <= :endTime AND r.endTime >= :startTime)
            ')
            ->setParameter('room', $room)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findConflictingReservations(Room $room, \DateTimeInterface $startTime, \DateTimeInterface $endTime): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.room = :room')
            ->andWhere('
                (r.startTime <= :endTime AND r.endTime >= :startTime)
            ')
            ->setParameter('room', $room)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getResult();
    }

    public function findUserReservations(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :user')
            ->andWhere('r.endTime >= :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->orderBy('r.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return RoomReservation[] Returns an array of upcoming reservations for a room
     */
    public function findUpcomingReservations(Room $room): array
    {
        $now = new \DateTime();
        
        return $this->createQueryBuilder('r')
            ->andWhere('r.room = :room')
            ->andWhere('r.startTime >= :now')
            ->setParameter('room', $room)
            ->setParameter('now', $now)
            ->orderBy('r.startTime', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return RoomReservation[] Returns an array of today's reservations for a room
     */
    public function findTodayReservations(Room $room): array
    {
        $today = new \DateTime('today');
        $tomorrow = new \DateTime('tomorrow');
        
        return $this->createQueryBuilder('r')
            ->andWhere('r.room = :room')
            ->andWhere('r.startTime >= :today')
            ->andWhere('r.startTime < :tomorrow')
            ->setParameter('room', $room)
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow)
            ->orderBy('r.startTime', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(RoomReservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RoomReservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
