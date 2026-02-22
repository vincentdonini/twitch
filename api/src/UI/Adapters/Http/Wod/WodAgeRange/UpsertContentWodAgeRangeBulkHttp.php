<?php

namespace App\UI\Adapters\Http\Wod\WodAgeRange;

use App\Domain\Wod\WodAgeRange\UpsertContentWodAgeRangeBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentWodAgeRangeBulkHttp implements UpsertContentWodAgeRangeBulkDTOInterface
{
    public function __construct(
        private Uuid  $id,
        private array $payload,
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