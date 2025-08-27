<?php

namespace App\Domain\Wod\Repository;

use App\Domain\Wod\Entity\WodVersionVariantExercise;

interface WodVersionVariantExerciseRepositoryInterface
{
    public function findOneById(string $id): ?WodVersionVariantExercise;

    public function findOneBySlug(string $slug): ?WodVersionVariantExercise;

    /** @return WodVersionVariantExercise[] */
    public function findAll(): array;

    public function save(WodVersionVariantExercise $wodVersionVariantExercise): void;

    public function delete(WodVersionVariantExercise $wodVersionVariantExercise): void;
}
