<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Find upcoming events
     */
    public function findUpcoming(int $limit = 10): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('e')
            ->andWhere('e.date >= :now')
            ->setParameter('now', $now)
            ->orderBy('e.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve tous les événements auxquels un utilisateur participe
     */
    public function findParticipatingEvents(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.participants', 'p')
            ->andWhere('p = :user')
            ->setParameter('user', $user)
            ->orderBy('e.date', 'ASC') // Tri par date croissante
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(?\DateTime $startDate, ?\DateTime $endDate): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($startDate) {
            $qb->andWhere('e.date >= :startDate')
                ->setParameter('startDate', $startDate->format('Y-m-d'));
        }

        if ($endDate) {
            $qb->andWhere('e.date <= :endDate')
                ->setParameter('endDate', $endDate->format('Y-m-d'));
        }

        return $qb->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
