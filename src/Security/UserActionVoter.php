<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\UserAction;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserActionVoter extends Voter
{
    public const COMPLETE = 'USER_ACTION_COMPLETE';
    public const VIEW     = 'USER_ACTION_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::COMPLETE, self::VIEW], true)
            && $subject instanceof UserAction;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var UserAction $userAction */
        $userAction = $subject;

        return match ($attribute) {
            self::COMPLETE => $this->canComplete($userAction, $user),
            self::VIEW     => $this->canView($userAction, $user),
            default        => false,
        };
    }

    private function canComplete(UserAction $userAction, User $user): bool
    {
        return $userAction->getUser() === $user
            && $userAction->getStatus() === 'A faire';
    }

    private function canView(UserAction $userAction, User $user): bool
    {
        return $userAction->getUser() === $user;
    }
}
