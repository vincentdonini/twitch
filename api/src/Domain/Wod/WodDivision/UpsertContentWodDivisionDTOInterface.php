<?php

namespace App\Domain\Wod\WodDivision;

interface UpsertContentWodDivisionDTOInterface
{
    public function getId(): string;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
