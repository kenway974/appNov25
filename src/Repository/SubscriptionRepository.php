<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }
    
    /**
     * Récupère la souscription d’un utilisateur (si elle existe)
     */
    public function findOneByUserId(int $userId): ?Subscription
    {
        return $this->createQueryBuilder('s')
            ->join('s.user', 'u')
            ->andWhere('u.id = :uid')
            ->setParameter('uid', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
