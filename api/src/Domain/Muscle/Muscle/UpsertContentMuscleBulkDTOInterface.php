<?php

namespace App\Domain\Muscle\Muscle;

use Symfony\Component\Uid\Uuid;

interface UpsertContentMuscleBulkDTOInterface
{
    public function getId(): Uuid;

    public function getContents(): array;
}
