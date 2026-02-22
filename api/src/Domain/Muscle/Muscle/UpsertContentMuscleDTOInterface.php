<?php

namespace App\Domain\Muscle\Muscle;

use Symfony\Component\Uid\Uuid;

interface UpsertContentMuscleDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
