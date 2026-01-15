<?php

namespace App\UI\Adapters\Http\Wod\WodAgeRange;

use App\Domain\Wod\WodAgeRange\UpsertContentWodAgeRangeBulkDTOInterface;

final readonly class UpsertContentWodAgeRangeBulkHttp implements UpsertContentWodAgeRangeBulkDTOInterface
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