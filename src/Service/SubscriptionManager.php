<?php

namespace App\Service;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionManager
{
    private SubscriptionRepository $subscriptionRepository;
    private EntityManagerInterface $em;

    public function __construct(SubscriptionRepository $subscriptionRepository, EntityManagerInterface $em)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->em = $em;
    }

    /**
     * Déactive toutes les subscriptions expirées
     *
     * @return int Nombre de subscriptions modifiées
     */
    public function deactivateExpiredSubscriptions(): int
    {
        $expiredSubscriptions = $this->subscriptionRepository->findExpiredSubscriptions();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->setIsActive(false);
            $subscription->setStatus('expired');
        }

        if ($expiredSubscriptions) {
            $this->em->flush();
        }

        return count($expiredSubscriptions);
    }
}
