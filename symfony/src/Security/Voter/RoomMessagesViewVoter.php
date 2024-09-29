<?php

namespace App\Security\Voter;

use App\Entity\Room;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RoomMessagesViewVoter extends Voter
{
    public const VIEW = 'room_membership';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute == self::VIEW
            && $subject instanceof Room;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if ($subject instanceof Room) {
            return $subject->getMembers()->contains($user);
        }

        return false;
    }
}
