<?php

namespace App\Domain\Wod\Repository;

use App\Domain\Wod\Entity\Wod;

interface WodRepositoryInterface
{
    public function findOneById(string $id): ?Wod;

    public function findOneByName(string $name): ?Wod;

    /** @return Wod[] */
    public function findAll(): array;

    /** @return Wod[] */
    public function findByFilters(
        string  $sortBy,
        string  $sortOrder,
        int     $offset,
        int     $limit,
        ?string $search,
        array   $filters = [],
    ): array;

    public function save(Wod $wod): void;

    public function delete(Wod $wod): void;
}
