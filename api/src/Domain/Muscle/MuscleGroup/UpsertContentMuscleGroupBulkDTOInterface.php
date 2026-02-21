<?php

namespace App\Domain\Muscle\MuscleGroup;

interface UpsertContentMuscleGroupBulkDTOInterface
{
    public function getId(): string;

    public function getContents(): array;
}
