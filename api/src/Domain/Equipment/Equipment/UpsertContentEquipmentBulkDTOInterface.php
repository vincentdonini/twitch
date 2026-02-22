<?php

namespace App\Domain\Equipment\Equipment;

use Symfony\Component\Uid\Uuid;

interface UpsertContentEquipmentBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
