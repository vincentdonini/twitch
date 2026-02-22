<?php

namespace App\Domain\Wod\WodCategory;

use Symfony\Component\Uid\Uuid;

interface UpsertContentWodCategoryBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
