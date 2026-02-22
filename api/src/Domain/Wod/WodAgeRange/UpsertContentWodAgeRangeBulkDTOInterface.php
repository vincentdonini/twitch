<?php

namespace App\Domain\Wod\WodAgeRange;

use Symfony\Component\Uid\Uuid;

interface UpsertContentWodAgeRangeBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
