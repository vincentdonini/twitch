<?php

namespace App\UI\Adapters\Http\Muscle\MuscleArea;

use App\Domain\Muscle\MuscleArea\UpsertContentMuscleAreaBulkDTOInterface;

final readonly class UpsertContentMuscleAreaBulkHttp implements UpsertContentMuscleAreaBulkDTOInterface
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