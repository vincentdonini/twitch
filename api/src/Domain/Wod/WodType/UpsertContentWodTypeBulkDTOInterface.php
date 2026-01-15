<?php

namespace App\Domain\Wod\WodType;

interface UpsertContentWodTypeBulkDTOInterface
{
    public function getId(): string;
    public function getContents(): array;
}
