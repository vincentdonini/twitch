<?php

namespace App\Infrastructure\Security\Voters;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractVoter extends Voter
{
    public function __construct(
        protected Security        $security,
        protected LoggerInterface $logger,
    ) {
    }

    protected function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }

    protected function hasRole(string $role): bool
    {
        return $this->getUser() && in_array($role, $this->getUser()->getRoles(), true);
    }

    protected function hasAnyRole(array $roles): bool
    {
        return (bool)array_intersect($roles, $this->getUser()?->getRoles() ?? []);
    }

    protected function isOwner(?string $ownerId): bool
    {
        if (!$this->getUser() || !$ownerId) {
            return false;
        }

        return (string)$this->getUser()->getId() === $ownerId;
    }
}
