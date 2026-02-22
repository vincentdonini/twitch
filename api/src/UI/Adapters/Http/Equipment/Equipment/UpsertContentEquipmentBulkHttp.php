<?php

namespace App\UI\Adapters\Http\Equipment\Equipment;

use App\Domain\Equipment\Equipment\UpsertContentEquipmentBulkDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpsertContentEquipmentBulkHttp implements UpsertContentEquipmentBulkDTOInterface
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
