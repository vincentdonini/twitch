<?php

namespace App\Domain\Achievement\AchievementCategory;

interface CreateAchievementCategoryDTOInterface
{
    public function getCode(): ?string;

    public function getPosition(): ?int;
}
