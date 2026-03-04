<?php

namespace App\Domain\Equipment\Equipment;

use Symfony\Component\Uid\Uuid;

interface UpsertContentEquipmentDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
