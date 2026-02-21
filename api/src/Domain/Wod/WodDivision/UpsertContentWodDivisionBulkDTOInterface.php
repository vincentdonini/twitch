<?php

namespace App\Domain\Wod\WodDivision;

interface UpsertContentWodDivisionBulkDTOInterface
{
    public function getId(): string;

    public function getContents(): array;
}
