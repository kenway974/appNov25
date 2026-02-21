<?php

namespace App\Service;

use App\Entity\Need;
use App\Entity\User;
use App\Entity\UserNeed;
use App\Repository\UserNeedRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserNeedManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserNeedRepository $userNeedRepo
    ) {}

    /**
     * Crée un UserNeed à partir des données d'un formulaire manuel.
     *
     * @param User  $user
     * @param Need  $need
     * @param array $data  (par ex. $_POST ou $request->request->all())
     */
    public function createUserNeed(User $user, Need $need, array $data): UserNeed
    {
        $userNeed = new UserNeed();
        $userNeed->setUser($user);
        $userNeed->setNeed($need);

        $priority = (int) ($data['priority'] ?? 0);
        if ($priority < 0 || $priority > 100) {
            throw new \InvalidArgumentException('La priorité doit être comprise entre 0 et 100.');
        }

        $userNeed->setPriority($priority);
        $userNeed->setScore(100 - $priority);
        $userNeed->setLastUpdated(new \DateTime());

        $this->em->persist($userNeed);
        $this->em->flush();

        return $userNeed;
    }

    /**
     * Met à jour les scores des UserNeed selon leur priorité et la dernière mise à jour.
     *
     * @param int|null $daysOverride Optionnel : force le nombre de jours écoulés (utile pour tests)
     * @return int Nombre de UserNeed mis à jour
     */
    public function updateScores(): void
    {
        // init date, sort tous les UserNeed en BDD
        $now = new \DateTime();
        $userNeeds = $this->userNeedRepo->findAll();

        // pour chaque UserNeed
        foreach ($userNeeds as $userNeed) {
            /** @var UserNeed $userNeed */
            // calcule le nombre de jours écoulés depuis la dernière mise à jour
            $last = $userNeed->getLastUpdated();
            $days = $last->diff($now)->days;

            // si au moins 1 jour s'est écoulé, décrémente le score en fonction de la priorité
            if ($days >= 1) {
                $decrement = $days * $userNeed->getPriority();
                $newScore = max(0, $userNeed->getScore() - $decrement);

                $userNeed->setScore($newScore);
                $userNeed->setLastUpdated($now);
            }
        }

        $this->em->flush();

        return;
    }
}
