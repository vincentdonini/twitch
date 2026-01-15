<?php

namespace App\UI\Adapters\Http\Muscle\MuscleGroup;

use App\Domain\Muscle\MuscleGroup\UpsertContentMuscleGroupBulkDTOInterface;

final readonly class UpsertContentMuscleGroupBulkHttp implements UpsertContentMuscleGroupBulkDTOInterface
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