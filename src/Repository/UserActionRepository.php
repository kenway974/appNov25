<?php

namespace App\Repository;

use App\Entity\UserAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAction>
 */
class UserActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAction::class);
    }

    /**
     * Retourne un UserAction par son id
     *
     * @param int $id
     * @return UserAction|null
     */
    public function findById(int $id): ?UserAction
    {
        return $this->createQueryBuilder('ua')
            ->andWhere('ua.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return UserAction[]
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('ua')
            ->andWhere('ua.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('ua.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les actions dont la deadline est passée
     * et qui n'ont pas encore de notification liée
     *
     * @param \DateTimeInterface $now
     * @return UserAction[]
     */
    public function findPastDeadlines(\DateTimeInterface $now): array
    {
        return $this->createQueryBuilder('ua')
            ->andWhere('ua.deadline < :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }

}
