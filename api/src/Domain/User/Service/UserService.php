<?php

namespace App\Domain\User\Service;

use App\Application\User\DTO\UserDTO;
use App\Domain\User\Entity\User;

final class UserService
{
    public function __construct(
    ) {

    }

    public function transformToDTO(User $user): userDTO
    {
        return new userDTO(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
        );
    }
}
