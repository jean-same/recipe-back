<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RecipeVoter extends Voter
{
    public const OWNER = 'CURRENT_RECIPE_OWNER';
    public const ADD = 'RECIPE_ADD';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::OWNER, self::ADD])
            && $subject instanceof \App\Entity\Recipe;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::OWNER:
                return $subject->getUser() == $user;
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case self::ADD:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
