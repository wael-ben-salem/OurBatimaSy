<?php

namespace App\Security;

use App\Entity\Planning;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PlanningVoter extends Voter
{
    const EDIT = 'edit';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::EDIT && $subject instanceof Planning;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) return false;

        /** @var Planning $planning */
        $planning = $subject;
        $note = $planning->getNote();

        return $user === $note->getCreatedBy() ||
            $user === $note->getAssignedTo() ||
            $user->hasRole('ROLE_ADMIN');
    }
}