<?php

namespace App\Domain\Exercise\Exercise;

use Symfony\Component\Uid\Uuid;

interface UpsertContentExerciseBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
