<?php

namespace App\UI\Adapters\Http\Wod\WodDivision;

use App\Domain\Wod\WodDivision\UpsertContentWodDivisionBulkDTOInterface;

final readonly class UpsertContentWodDivisionBulkHttp implements UpsertContentWodDivisionBulkDTOInterface
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