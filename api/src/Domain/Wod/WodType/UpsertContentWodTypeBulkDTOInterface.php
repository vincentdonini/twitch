<?php

namespace App\Domain\Wod\WodType;

use Symfony\Component\Uid\Uuid;

interface UpsertContentWodTypeBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
