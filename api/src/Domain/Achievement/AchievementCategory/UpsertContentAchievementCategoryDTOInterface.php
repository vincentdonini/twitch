<?php

namespace App\Domain\Achievement\AchievementCategory;

use Symfony\Component\Uid\Uuid;

interface UpsertContentAchievementCategoryDTOInterface
{
    public function getId(): Uuid;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getDescription(): ?string;
}
