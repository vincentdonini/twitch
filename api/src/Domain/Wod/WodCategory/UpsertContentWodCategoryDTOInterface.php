<?php

namespace App\Domain\Wod\WodCategory;

use Symfony\Component\Uid\Uuid;

interface UpsertContentWodCategoryDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
