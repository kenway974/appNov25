<?php

namespace App\Service;

use App\Entity\Need;
use App\Entity\User;
use App\Entity\UserNeed;
use Doctrine\ORM\EntityManagerInterface;

class UserNeedManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
 
    /**
     * Crée un UserNeed à partir des données d'un formulaire manuel.
     *
     * @param User   $user
     * @param Need   $need
     * @param array  $data  (par ex. $_POST ou $request->request->all())
     */
    public function createUserNeed(User $user, Need $need, array $data): UserNeed
    {
        $userNeed = new UserNeed();
        $userNeed->setUser($user);
        $userNeed->setNeed($need);

        // On récupère les champs envoyés dans le form manuel
        $priority = $data['priority'] ?? 0;

        $userNeed->setPriority((int) $priority);
        $userNeed->setScore(100 - (int) $priority);
        $userNeed->setLastUpdated(new \DateTime());

        $this->em->persist($userNeed);
        $this->em->flush();

        return $userNeed;
    }
}
