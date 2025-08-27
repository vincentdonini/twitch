<?php

namespace App\Domain\Wod\Repository;

use App\Domain\Wod\Entity\WodType;

interface WodTypeRepositoryInterface
{

    public function findOneById(string $id): ?WodType;
    public function findOneBySlug(string $slug): ?WodType;

    /** @return WodType[] */
    public function findAll(): array;

    /** @return WodType[] */
    public function findByFilters(
        string $sortBy,
        string $sortOrder,
        int    $offset,
        int    $limit,
    ): array;

    public function save(WodType $wodType): void;

    public function delete(WodType $wodType): void;
}
