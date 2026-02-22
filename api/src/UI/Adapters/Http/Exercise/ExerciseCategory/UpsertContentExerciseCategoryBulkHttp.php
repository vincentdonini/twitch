<?php

namespace App\UI\Adapters\Http\Exercise\ExerciseCategory;

use App\Domain\Exercise\ExerciseCategory\UpsertContentExerciseCategoryBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentExerciseCategoryBulkHttp implements UpsertContentExerciseCategoryBulkDTOInterface
{
    public function __construct(
        private Uuid $id,
        private array  $payload,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getContents(): array
    {
        return $this->payload;
    }
}
