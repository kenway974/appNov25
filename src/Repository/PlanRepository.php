<?php

namespace App\Repository;

use App\Entity\Plan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plan>
 */
class PlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
    }

    /**
     * Retourne tous les plans actifs, triés par prix croissant
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.price', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les plans avec un prix supérieur à 0
     *
     * @return Plan[]
     */
    public function findPaidPlans(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.price > 0')
            ->getQuery()
            ->getResult();
    }
}
