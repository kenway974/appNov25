<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Need;
use App\Entity\UserNeed;
use Doctrine\ORM\EntityManagerInterface;

class UserNeedService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Crée un UserNeed à partir d'un user, d'un need et de données supplémentaires
     */
    public function handleUserNeedForm(User $user, Need $need, array $data): UserNeed
    {
        $userNeed = new UserNeed();
        $userNeed->setUser($user);
        $userNeed->setNeed($need);

        if (isset($data['priority'])) {
            $userNeed->setPriority((int)$data['priority']);
        }

        if (isset($data['score'])) {
            $userNeed->setScore((int)$data['score']);
        }

        $this->em->persist($userNeed);
        $this->em->flush();

        return $userNeed;
    }
}
