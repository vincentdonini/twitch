<?php

namespace App\Domain\Exercise\Repository;

use App\Domain\Exercise\Entity\Exercise;

interface ExerciseRepositoryInterface
{
    public function findOneById(string $id): ?Exercise;

    public function findOneByName(string $name): ?Exercise;

    /** @return Exercise[] */
    public function findAll(): array;

    /** @return Exercise[] */
    public function findByFilters(
        string  $sortBy,
        string  $sortOrder,
        int     $offset,
        int     $limit,
        ?string $search,
    ): array;

    public function save(Exercise $exercise): void;

    public function delete(Exercise $exercise): void;
}
