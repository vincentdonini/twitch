<?php

namespace App\Domain\Wod\Repository;

use App\Domain\Wod\Entity\WodVersion;

interface WodVersionRepositoryInterface
{
    public function findOneById(string $id): ?WodVersion;

    public function findOneByName(string $name): ?WodVersion;

    /** @return WodVersion[] */
    public function findAll(): array;

    /** @return WodVersion[] */
    public function findByFilters(
        string  $sortBy,
        string  $sortOrder,
        int     $offset,
        int     $limit,
        ?string $search = null,
        array   $filters = [],
    ): array;

    public function save(WodVersion $wodVersion): void;

    public function delete(WodVersion $wodVersion): void;
}
