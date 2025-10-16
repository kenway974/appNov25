<?php

namespace App\Repository;

use App\Entity\Block;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Block>
 */
class BlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Block::class);
    }

    public function findByUserAction(int $userActionId): array
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.actions', 'a') // relation ManyToMany Block -> Action
            ->andWhere('a.id = :userActionId')
            ->setParameter('userActionId', $userActionId)
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les blocs d'un type spécifique
     *
     * @param string $type
     * @return Block[]
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.type = :type')
            ->setParameter('type', $type)
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

}