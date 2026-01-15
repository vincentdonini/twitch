<?php

namespace App\UI\Adapters\Http\Equipment\Equipment;

use App\Domain\Equipment\Equipment\UpsertContentEquipmentBulkDTOInterface;

final readonly class UpsertContentEquipmentBulkHttp implements UpsertContentEquipmentBulkDTOInterface
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