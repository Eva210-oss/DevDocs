<?php

namespace App\Security;

use App\Entity\Tutorial;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TutorialVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Tutorial
            && in_array($attribute, [self::EDIT, self::DELETE]);
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $user = $token->getUser();

        // L'utilisateur doit être connecté
        if (!$user instanceof User) {
            return false;
        }

        // Le tutoriel concerné
        /** @var Tutorial $tutorial */
        $tutorial = $subject;

        // Vérifier si l'utilisateur est l'auteur
        if ($tutorial->getAuthor() !== $user) {
            return false;
        }

        return true;
    }
}