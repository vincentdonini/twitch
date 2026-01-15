<?php

namespace App\Infrastructure\Security\Voters\Equipment;

use App\Domain\Equipment\Entity\Equipment;
use App\Infrastructure\Security\Voters\AbstractVoter;
use App\Infrastructure\Security\Voters\ListPermissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class EquipmentVoter extends AbstractVoter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === ListPermissions::PERMISSION_EQUIPMENT_VIEW
            && $subject instanceof Equipment;
    }

    protected function voteOnAttribute(
        string         $attribute,
        mixed          $subject,
        TokenInterface $token
    ): bool {
        /** @var Equipment $equipment */
        $equipment = $subject;

        return match ($attribute) {
            ListPermissions::PERMISSION_EQUIPMENT_VIEW => $this->canView($equipment),
            default => false,
        };
    }

    private function canView(Equipment $equipment): bool
    {
        // ADMIN
        if ($this->hasRole('ROLE_ADMIN')) {
            return true;
        }

//        // propriétaire
//        if ($this->isOwner($equipment->getOwner()->getId())) {
//            return true;
//        }
//
//        // public
//        return !$equipment->isPrivate();
    }
}

