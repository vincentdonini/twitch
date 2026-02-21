<?php

namespace App\Domain\Achievement\AchievementCategory;

use Symfony\Component\Uid\Uuid;

interface GetAchievementCategoryByIdDTOInterface
{
    public function getId(): Uuid;
}
