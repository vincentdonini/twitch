<?php

namespace App\Domain\Movement\Repository;

use App\Domain\Movement\Entity\Movement;

interface MovementRepositoryInterface
{
    public function findOneById(string $id): ?Movement;

    public function findOneBySlug(string $slug): ?Movement;

    /** @return Movement[] */
    public function findAll(): array;

    /** @return Movement[] */
    public function findByFilters(
        string $sortBy,
        string $sortOrder,
        int    $offset,
        int    $limit,
    ): array;

    public function save(Movement $movement): void;

    public function delete(Movement $movement): void;
}
