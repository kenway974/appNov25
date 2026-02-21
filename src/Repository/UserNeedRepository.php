<?php

namespace App\Repository;

use App\Entity\UserNeed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserNeed>
 */
class UserNeedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserNeed::class);
    }

    /**
 * @return UserNeed[]
 */
public function findByUserId(int $userId): array
{
    return $this->createQueryBuilder('un')
        ->andWhere('un.user = :userId')
        ->setParameter('userId', $userId)
        ->orderBy('un.priority', 'ASC')
        ->getQuery()
        ->getResult();

    /*
    $dql = 'SELECT un 
            FROM App\Entity\UserNeed un 
            WHERE un.user = :userId 
            ORDER BY un.priority ASC';

    return $this->getEntityManager()
                ->createQuery($dql)
                ->setParameter('userId', $userId)
                ->getResult();
    */
}


}
