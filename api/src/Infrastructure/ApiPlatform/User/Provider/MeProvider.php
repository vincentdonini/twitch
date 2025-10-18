<?php

namespace App\Infrastructure\ApiPlatform\User\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Domain\User\Entity\User;

class MeProvider implements ProviderInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): User
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new AccessDeniedException('User not authenticated.');
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedException('Invalid user.');
        }

        return $user;
    }
}
