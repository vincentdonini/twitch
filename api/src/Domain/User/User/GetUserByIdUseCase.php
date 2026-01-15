<?php

namespace App\Domain\User\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Ports\UserDALInterface;
use Doctrine\ORM\EntityNotFoundException;

readonly class GetUserByIdUseCase
{
    public function __construct(
        private UserDALInterface $userDAL,
    ) {
    }

    public function execute(GetUserByIdDTOInterface $dto): User
    {
        $user = $this->userDAL->getById($dto->getId());
        if (!$user instanceof User) {
            throw new EntityNotFoundException();
        }

        return $user;
    }
}

