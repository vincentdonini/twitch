<?php

namespace App\Domain\Wod\WodCategory;

interface UpsertContentWodCategoryBulkDTOInterface
{
    public function getId(): string;
    public function getContents(): array;
}
