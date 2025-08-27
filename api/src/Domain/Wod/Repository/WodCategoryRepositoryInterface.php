<?php

namespace App\Domain\Wod\Repository;

use App\Domain\Wod\Entity\WodCategory;

interface WodCategoryRepositoryInterface
{
    public function findOneById(string $id): ?WodCategory;
    public function findOneBySlug(string $slug): ?WodCategory;

    /** @return WodCategory[] */
    public function findAll(): array;

    /** @return WodCategory[] */
    public function findByFilters(
        string $sortBy,
        string $sortOrder,
        int    $offset,
        int    $limit,
    ): array;

    public function save(WodCategory $wodCategory): void;

    public function delete(WodCategory $wodCategory): void;
}
