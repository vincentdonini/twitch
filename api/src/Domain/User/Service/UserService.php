<?php

namespace App\Domain\User\Service;

use App\Application\Common\CollectionMapper;
use App\Application\User\DTO\UserDTO;
use App\Domain\User\Entity\User;
use App\Infrastructure\Filters\FilterCollection;

class UserService
{
    public function transformToDTO(User $user, FilterCollection $filters = null): UserDTO
    {
        return new UserDTO(
            id         : $user->getId(),
            email      : $user->getEmail(),
            firstName  : $user->getFirstName(),
            lastName   : $user->getLastName(),
            roles      : $user->getRoles(),
            permissions: $user->getPermissions(),
        );
    }

    public function transformCollectionToDTO(array $users, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $users,
            fn(User $user) => $this->transformToDTO($user, $filters)
        );
    }
}
