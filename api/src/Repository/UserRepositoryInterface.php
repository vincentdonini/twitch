<?php

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{
    /** @return User[] */
    public function findAllUsers(): array;

    public function findUserById(int $id): ?User;

    public function delete(User $user): void;
}
