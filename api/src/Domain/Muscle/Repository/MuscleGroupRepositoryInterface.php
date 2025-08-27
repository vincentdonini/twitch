<?php

namespace App\Domain\Muscle\Repository;

use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Entity\MuscleGroup;

interface MuscleGroupRepositoryInterface
{
    public function findOneById(string $id): ?MuscleGroup;
    public function findOneBySlug(string $slug): ?MuscleGroup;

    /** @return MuscleGroup[] */
    public function findAll(): array;

    /** @return MuscleGroup[] */
    public function findAllByAreaId(string $areaId): array;

    public function findOneByAreaId(string $groupId, string $areaId): ?MuscleGroup;

    /** @return MuscleGroup[] */
    public function findByFilters(
        string $sortBy,
        string $sortOrder,
        int    $offset,
        int    $limit,
    ): array;

    public function save(MuscleGroup $muscleGroup): void;

    public function delete(MuscleGroup $muscleGroup): void;
}
