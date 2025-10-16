<?php

namespace App\Repository;

use App\Entity\Action;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Action>
 */
class ActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Action::class);
    }

    /**
     * Récupère les actions par type
     *
     * @param string $type
     * @return Action[]
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.type = :type')
            ->setParameter('type', $type)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les actions par intension
     *
     * @param string $intension
     * @return Action[]
     */
    public function findByIntension(string $intension): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.intension = :intension')
            ->setParameter('intension', $intension)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les actions faisables maintenant
     *
     * @param bool $isDoable
     * @return Action[]
     */
    public function findByIsDoableNow(bool $isDoable): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isDoableNow = :doable')
            ->setParameter('doable', $isDoable)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
