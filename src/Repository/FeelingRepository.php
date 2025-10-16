<?php

namespace App\Repository;

use App\Entity\Feeling;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feeling>
 */
class FeelingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feeling::class);
    }

    /**
     * @return Feeling[]
     */
    public function findByEmotion(string $emotion): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.emotion = :emotion')
            ->setParameter('emotion', $emotion)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    //    public function findOneBySomeField($value): ?Feeling
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
