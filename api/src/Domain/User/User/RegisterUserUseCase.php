<?php

namespace App\Domain\User\User;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Ports\UserDALInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class RegisterUserUseCase
{
    public function __construct(
        private DatabaseInterface           $database,
        private UserDALInterface            $userDAL,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function execute(RegisterUserDTOInterface $dto): User
    {
        if (!$dto->getEmail() || !$dto->getPassword() || !$dto->getFirstName() || !$dto->getLastName()) {
            throw new InvalidPayloadException('Missing required identifiers.');
        }

        if ($this->userDAL->findByEmail($dto->getEmail()) !== null) {
            throw new AlreadyExistException('Email already exists.');
        }

        $user = new User($dto->getEmail(), $dto->getFirstName(), $dto->getLastName());
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->getPassword()));

        $this->database->preSave($user);
        $this->database->save();

        return $user;
    }
}
