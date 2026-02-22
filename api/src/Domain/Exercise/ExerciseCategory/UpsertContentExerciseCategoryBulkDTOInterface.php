<?php

namespace App\Domain\Exercise\ExerciseCategory;

use Symfony\Component\Uid\Uuid;

interface UpsertContentExerciseCategoryBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
