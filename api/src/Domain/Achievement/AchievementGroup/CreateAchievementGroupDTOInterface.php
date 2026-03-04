<?php

namespace App\Domain\Achievement\AchievementGroup;

use Symfony\Component\Uid\Uuid;

interface CreateAchievementGroupDTOInterface
{
    public function getCode(): ?string;

    public function getPosition(): ?int;

    public function getAchievementCategoryId(): ?Uuid;
}
