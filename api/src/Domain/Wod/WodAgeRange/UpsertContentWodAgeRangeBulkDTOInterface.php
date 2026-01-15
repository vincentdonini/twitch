<?php

namespace App\Domain\Wod\WodAgeRange;

interface UpsertContentWodAgeRangeBulkDTOInterface
{
    public function getId(): string;
    public function getContents(): array;
}
