<?php

namespace App\Domain\Movement\Repository;

use App\Domain\Movement\Entity\MovementCategory;

interface MovementCategoryRepositoryInterface
{
    public function findOneById(string $id): ?MovementCategory;
    public function findOneBySlug(string $slug): ?MovementCategory;

    /** @return MovementCategory[] */
    public function findAll(): array;

    /** @return MovementCategory[] */
    public function findByFilters(
        string $sortBy,
        string $sortOrder,
        int    $offset,
        int    $limit,
        array  $typesArray,
    ): array;

    public function save(MovementCategory $movementCategory): void;

    public function delete(MovementCategory $movementCategory): void;
}
