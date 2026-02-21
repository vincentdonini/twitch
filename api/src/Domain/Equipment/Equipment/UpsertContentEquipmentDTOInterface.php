<?php

namespace App\Domain\Equipment\Equipment;

interface UpsertContentEquipmentDTOInterface
{
    public function getId(): string;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
