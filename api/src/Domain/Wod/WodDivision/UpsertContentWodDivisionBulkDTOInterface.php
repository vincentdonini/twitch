<?php

namespace App\Domain\Wod\WodDivision;

use Symfony\Component\Uid\Uuid;

interface UpsertContentWodDivisionBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
