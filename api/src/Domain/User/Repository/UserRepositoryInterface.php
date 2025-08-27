<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;

interface UserRepositoryInterface
{
    /** @return User[] */
    public function findAllUsers(): array;

    /** @return User[] */
    public function findByFilters(
        string  $sortBy,
        string  $sortOrder,
        int     $offset,
        int     $limit,
        ?string $search = null,
        array   $filters = [],
    ): array;

    public function findUserById(int $id): ?User;

    public function delete(User $user): void;
}
