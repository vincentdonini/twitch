<?php

namespace App\UI\Adapters\Http\Muscle\Muscle;

use App\Domain\Muscle\Muscle\UpsertContentMuscleBulkDTOInterface;

final readonly class UpsertContentMuscleBulkHttp implements UpsertContentMuscleBulkDTOInterface
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