<?php

namespace App\Domain\User\User;

use App\Domain\Core\Exceptions\InvalidPayloadException;
use App\Domain\Core\Ports\DatabaseInterface;
use App\Domain\User\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UpdatePasswordUseCase
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private DatabaseInterface           $database,
    ) {
    }

    public function execute(User $currentUser, UpdatePasswordDTOInterface $dto): void
    {
        if (!$this->passwordHasher->isPasswordValid($currentUser, $dto->getCurrentPassword())) {
            throw new InvalidPayloadException('Current password is incorrect.');
        }

        $hashed = $this->passwordHasher->hashPassword($currentUser, $dto->getNewPassword());
        $currentUser->setPassword($hashed);

        $this->database->preSave($currentUser);
        $this->database->save();
    }
}
