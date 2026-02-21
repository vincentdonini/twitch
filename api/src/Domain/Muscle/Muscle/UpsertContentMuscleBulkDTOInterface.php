<?php

namespace App\Domain\Muscle\Muscle;

interface UpsertContentMuscleBulkDTOInterface
{
    public function getId(): string;

    public function getContents(): array;
}
