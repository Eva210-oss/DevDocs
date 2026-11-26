<?php

namespace App\Security;

use App\Entity\Tutorial;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TutorialVoter extends Voter
{
    public const VIEW = 'TUTORIAL_VIEW';
    public const EDIT = 'TUTORIAL_EDIT';
    public const DELETE = 'TUTORIAL_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            self::VIEW,
            self::EDIT,
            self::DELETE,
        ]) && $subject instanceof Tutorial;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        /** @var Tutorial $tutorial */
        $tutorial = $subject;

        // Everyone can view public tutorials
        if ($attribute === self::VIEW) {
            return true;
        }

        $user = $token->getUser();

        // User must be authenticated
        if (!$user instanceof User) {
            return false;
        }

        // Only the author can edit or delete
        return $tutorial->getAuthor() === $user;
    }
}