<?php

namespace App\Domain\Muscle\Repository;

use App\Domain\Muscle\Entity\MuscleArea;

interface MuscleAreaRepositoryInterface
{
    public function findOneById(string $id): ?MuscleArea;

    public function findOneBySlug(string $slug): ?MuscleArea;

    /** @return MuscleArea[] */
    public function findAll(): array;

    /** @return MuscleArea[] */
    public function findByFilters(
        string $sortBy,
        string $sortOrder,
        int    $offset,
        int    $limit,
    ): array;

    public function save(MuscleArea $muscleArea): void;

    public function delete(MuscleArea $muscleArea): void;
}
