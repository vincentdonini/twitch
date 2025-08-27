<?php

namespace App\Domain\Equipment\Repository;

use App\Domain\Equipment\Entity\Equipment;

interface EquipmentRepositoryInterface
{
    public function findOneById(string $id): ?Equipment;

    public function findOneBySlug(string $slug): ?Equipment;

    /** @return Equipment[] */
    public function findAll(): array;

    /** @return Equipment[] */
    public function findByFilters(
        string  $sortBy,
        string  $sortOrder,
        int     $offset,
        int     $limit,
        ?string $search,
    ): array;

    public function save(Equipment $equipment): void;

    public function delete(Equipment $equipment): void;
}
