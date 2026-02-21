<?php

namespace App\Domain\Equipment\Equipment;

interface UpsertContentEquipmentBulkDTOInterface
{
    public function getId(): string;

    public function getContents(): array;
}
