<?php

namespace App\Domain\Exercise\Exercise;

interface UpsertContentExerciseBulkDTOInterface
{
    public function getId(): string;
    public function getContents(): array;
}
