<?php

namespace App\Domain\Achievement\AchievementGroup;

use Symfony\Component\Uid\Uuid;

interface GetAchievementGroupByIdDTOInterface
{
    public function getId(): Uuid;
}
