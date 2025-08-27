<?php

namespace App\Domain\Wod\Repository;

use App\Domain\Wod\Entity\WodVersionType;

interface WodVersionTypeRepositoryInterface
{

    public function findOneById(string $id): ?WodVersionType;
    public function findOneBySlug(string $slug): ?WodVersionType;

    /** @return WodVersionType[] */
    public function findAll(): array;

    /** @return WodVersionType[] */
    public function findByFilters(
        string $sortBy,
        string $sortOrder,
        int    $offset,
        int    $limit,
    ): array;

    public function save(WodVersionType $wodVersionType): void;

    public function delete(WodVersionType $wodVersionType): void;
}
