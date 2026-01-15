<?php

namespace App\UI\Adapters\Http\Wod\WodType;

use App\Domain\Wod\WodType\UpsertContentWodTypeBulkDTOInterface;

final readonly class UpsertContentWodTypeBulkHttp implements UpsertContentWodTypeBulkDTOInterface
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