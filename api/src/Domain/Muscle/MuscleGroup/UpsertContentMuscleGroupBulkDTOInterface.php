<?php

namespace App\Domain\Muscle\MuscleGroup;

use Symfony\Component\Uid\Uuid;

interface UpsertContentMuscleGroupBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
