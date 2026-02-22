<?php

namespace App\Domain\Wod\WodType;

use Symfony\Component\Uid\Uuid;

interface UpsertContentWodTypeDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
