<?php

namespace App\Domain\Achievement\AchievementGroup;

interface CreateAchievementGroupDTOInterface
{
    public function getCode(): ?string;

    public function getPosition(): ?int;

    public function getAchievementCategoryId(): ?string;
}
