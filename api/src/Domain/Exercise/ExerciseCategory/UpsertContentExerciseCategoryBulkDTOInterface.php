<?php

namespace App\Domain\Exercise\ExerciseCategory;

interface UpsertContentExerciseCategoryBulkDTOInterface
{
    public function getId(): string;

    public function getContents(): array;
}
