<?php

namespace App\Domain\Muscle\MuscleArea;

use Symfony\Component\Uid\Uuid;

interface UpsertContentMuscleAreaBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
