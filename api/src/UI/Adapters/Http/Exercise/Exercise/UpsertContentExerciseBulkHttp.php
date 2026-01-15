<?php

namespace App\UI\Adapters\Http\Exercise\Exercise;

use App\Domain\Exercise\Exercise\UpsertContentExerciseBulkDTOInterface;

final readonly class UpsertContentExerciseBulkHttp implements UpsertContentExerciseBulkDTOInterface
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