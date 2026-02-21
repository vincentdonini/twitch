<?php

namespace App\Domain\Wod\WodCategory;

interface UpsertContentWodCategoryDTOInterface
{
    public function getId(): string;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
