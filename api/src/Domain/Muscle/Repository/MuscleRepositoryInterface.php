<?php

namespace App\Domain\Muscle\Repository;

use App\Domain\Muscle\Entity\Muscle;

interface MuscleRepositoryInterface
{
    public function findOneById(string $id): ?Muscle;

    public function findOneBySlug(string $slug): ?Muscle;

    /** @return Muscle[] */
    public function findAll(): array;

    /** @return Muscle[] */
    public function findAllByGroupId(string $groupId): array;

    /** @return Muscle[] */
    public function findAllByAreaIdAndGroupId(string $areaId, string $groupId): array;

    /** @return Muscle[] */
    public function findByFilters(
        string  $sortBy,
        string  $sortOrder,
        int     $offset,
        int     $limit,
        ?string $search
    ): array;

    public function save(Muscle $muscle): void;

    public function delete(Muscle $muscle): void;
}
