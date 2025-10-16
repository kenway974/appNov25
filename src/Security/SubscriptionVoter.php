<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SubscriptionVoter extends Voter
{
    // Attribut que l’on va vérifier
    private const PREMIUM_ACCESS = 'PREMIUM_ACCESS';

    /**
     * Détermine si ce voter peut gérer l’attribut demandé
     */
    protected function supports(string $attribute, $subject): bool
    {
        // Ici on ne gère que PREMIUM_ACCESS
        return $attribute === self::PREMIUM_ACCESS;
    }

    /**
     * Vérifie si l’utilisateur a le droit
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Vérifie si l’utilisateur est connecté
        if (!$user instanceof User) {
            return false;
        }

        // Récupère le rôle dynamique
        $roles = $user->getRoles();

        // On accorde l’accès si l’utilisateur a ROLE_SUBSCRIBER
        return in_array('ROLE_SUBSCRIBER', $roles, true);
    }
}
