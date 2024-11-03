<?php

namespace App\Repository;

use App\Entity\MoodEntry;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MoodEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MoodEntry::class);
    }

    public function findByUserAndDateRange(User $user, \DateTimeInterface $startDate, \DateTimeInterface $endDate)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.user = :user')
            ->andWhere('m.date BETWEEN :startDate AND :endDate')
            ->setParameter('user', $user)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findMoodEntriesForUserByDateRange(User $user, \DateTimeInterface $startDate)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.user = :user')
            ->andWhere('m.date >= :startDate')
            ->setParameter('user', $user)
            ->setParameter('startDate', $startDate)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findMoodEntriesByDateRange(\DateTimeInterface $startDate)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.date >= :startDate')
            ->setParameter('startDate', $startDate)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
