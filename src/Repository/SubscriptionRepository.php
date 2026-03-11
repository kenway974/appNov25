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
     * Récupère toutes les subscriptions expirées encore actives
     *
     * @return Subscription[]
     */
    public function findExpiredSubscriptions(): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('s')
            ->andWhere('s.endDate <= :now')
            ->andWhere('s.isActive = :active')
            ->setParameter('now', $now)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère la subscription d'un utilisateur à partir de son email
     */
    public function findOneByUserEmail(string $email): ?Subscription
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.user', 'u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère la subscription d'un utilisateur à partir de l'entité User
     */
    public function findOneByUser($user): ?Subscription
    {
        return $this->createQueryBuilder('s')
        ->join('s.user', 'u')        // jointure explicite sur l'utilisateur
        ->andWhere('u = :user')      // on filtre sur l'utilisateur
        ->setParameter('user', $user)
        ->getQuery()
        ->getOneOrNullResult();
    }
}