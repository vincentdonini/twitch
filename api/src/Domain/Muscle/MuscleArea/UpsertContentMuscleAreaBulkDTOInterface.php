<?php

namespace App\Domain\Muscle\MuscleArea;

interface UpsertContentMuscleAreaBulkDTOInterface
{
    public function getId(): string;
    public function getContents(): array;
}
