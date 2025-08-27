<?php

namespace App\Domain\Exercise\Repository;

use App\Domain\Exercise\Entity\ExerciseCategory;

interface ExerciseCategoryRepositoryInterface
{
    public function findOneById(string $id): ?ExerciseCategory;
    public function findOneBySlug(string $slug): ?ExerciseCategory;

    /** @return ExerciseCategory[] */
    public function findAll(): array;

    /** @return ExerciseCategory[] */
    public function findByFilters(
        string $sortBy,
        string $sortOrder,
        int    $offset,
        int    $limit,
        array  $typesArray,
    ): array;

    public function save(ExerciseCategory $exerciseCategory): void;

    public function delete(ExerciseCategory $exerciseCategory): void;
}
