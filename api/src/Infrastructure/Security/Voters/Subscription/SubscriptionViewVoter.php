<?php

namespace App\Infrastructure\Security\Voters\Subscription;

use App\Domain\Organization\Entity\Place;
use App\Domain\Organization\Entity\Subscription;
use App\Domain\User\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SubscriptionViewVoter extends Voter
{
    public const VIEW = 'SUBSCRIPTION_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::VIEW
            && $subject instanceof Place;
    }

    protected function voteOnAttribute(
        string         $attribute,
        mixed          $subject,
        TokenInterface $token,
    ): bool {
        /** @var User|null $user */
        $user = $token->getUser();

        /** @var Place $place */
        $place = $subject;

        // If no user is logged in, we refuse
        if (!$user instanceof User) {
            return false;
        }

        // The admins have all rights
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // Owners and coaches at the venue can create subscriptions
        if ($place->isOwner($user) || $place->isCoach($user)) {
            return true;
        }

        // Otherwise, access denied
        return false;
    }
}