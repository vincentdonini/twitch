<?php

namespace App\UI\Adapters\Http\Wod\WodType;

use App\Domain\Wod\WodType\UpsertContentWodTypeBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentWodTypeBulkHttp implements UpsertContentWodTypeBulkDTOInterface
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
