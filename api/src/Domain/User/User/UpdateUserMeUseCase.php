<?php

namespace App\Domain\User\User;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Ports\UserDALInterface;

final readonly class UpdateUserMeUseCase
{
    public function __construct(
        private UserDALInterface  $userDAL,
        private DatabaseInterface $database,
    ) {
    }

    public function execute(User $currentUser, UpdateUserMeDTOInterface $dto): User
    {
        if ($dto->getEmail() !== null && $dto->getEmail() !== $currentUser->getEmail()) {
            $existing = $this->userDAL->findByEmail($dto->getEmail());
            if ($existing !== null && !$existing->getId()->equals($currentUser->getId())) {
                throw new AlreadyExistException('Email already in use.');
            }
            $currentUser->setEmail($dto->getEmail());
        }

        if ($dto->getFirstName() !== null && $dto->getFirstName() !== '') {
            $currentUser->setFirstName($dto->getFirstName());
        }

        if ($dto->getLastName() !== null && $dto->getLastName() !== '') {
            $currentUser->setLastName($dto->getLastName());
        }

        $this->database->preSave($currentUser);
        $this->database->save();

        return $currentUser;
    }
}
