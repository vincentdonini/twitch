<?php

namespace App\UI\Adapters\Http\Exercise\ExerciseCategory;

use App\Domain\Exercise\ExerciseCategory\UpsertContentExerciseCategoryBulkDTOInterface;

final readonly class UpsertContentExerciseCategoryBulkHttp implements UpsertContentExerciseCategoryBulkDTOInterface
{
    public function __construct(
        private string $id,
        private array  $payload,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContents(): array
    {
        return $this->payload;
    }
}