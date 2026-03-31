<?php

namespace App\Repository;

use App\Document\Notification;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * NotificationRepository (doctrrine/mongodb-odm) pour gérer les notifications dans MongoDB.
 */
class NotificationRepository extends DocumentRepository
{    
    /**
     * Récupère notifs d'un  utilisateur,
     * triées par date décroissante.
     * 
     * @param int $userId
     * @param int|null $limit
     *
     * @return iterable Collection
     */
    public function findByUser(int $userId, int $limit = null): iterable
    {
        $qb = $this->createQueryBuilder()

            ->field('userId')->equals($userId)
            ->sort('createdAt', 'desc');

        if ($limit !== null) {
            $qb->limit($limit);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * Récupère les notifs d'un utilisateur
     * filtrées par context
     * triées par date décroissante.
     *
     * context = { "actionId": 5 } distingue notifs action ou need
     *
     * @param int $userId
     * @param string $contextKey
     * @param mixed $contextValue
     * @param int|null $limit
     *
     * @return iterable
     */
    public function findByUserAndContext(
        int $userId,
        string $contextKey,
        mixed $contextValue,
        int $limit = null
    ): iterable {
        $qb = $this->createQueryBuilder()

            ->field('userId')->equals($userId)
            ->field("context.$contextKey")->equals($contextValue)
            ->sort('createdAt', 'desc');

        if ($limit !== null) {
            $qb->limit($limit);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * Récupère les notifications d’un utilisateur
     * selon leur état de lecture (lues ou non lues),
     * triées par date décroissante.
     *
     * @param int $userId
     * @param bool $isRead true = lues, false = non lues
     * @param int|null $limit
     *
     * @return iterable
     */
    public function findByIsRead(int $userId, bool $isRead, int $limit = null): iterable
    {
        $qb = $this->createQueryBuilder()

            // Filtre utilisateur
            ->field('userId')->equals($userId)

            // Filtre état de lecture
            ->field('isRead')->equals($isRead)

            // Tri par date décroissante
            ->sort('createdAt', 'desc');

        if ($limit !== null) {
            $qb->limit($limit);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * Retourne la dernière notification 
     * pour éviter les doublons
     */
    public function findLatestDeadlineForUserAction(
        int $userId,
        int $userActionId
    ): ?Notification {

        return $this->createQueryBuilder()
            ->field('userId')->equals($userId)
            ->field('type')->equals('deadline')
            ->field('context.user_action_id')->equals($userActionId)
            ->sort('createdAt', 'DESC')
            ->limit(1)
            ->getQuery()
            ->getSingleResult();
    }
}
