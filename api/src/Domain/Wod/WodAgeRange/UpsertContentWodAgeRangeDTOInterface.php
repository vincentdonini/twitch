<?php

namespace App\Domain\Wod\WodAgeRange;

use Symfony\Component\Uid\Uuid;

interface UpsertContentWodAgeRangeDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
