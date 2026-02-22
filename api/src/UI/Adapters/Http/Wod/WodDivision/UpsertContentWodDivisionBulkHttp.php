<?php

namespace App\UI\Adapters\Http\Wod\WodDivision;

use App\Domain\Wod\WodDivision\UpsertContentWodDivisionBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentWodDivisionBulkHttp implements UpsertContentWodDivisionBulkDTOInterface
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