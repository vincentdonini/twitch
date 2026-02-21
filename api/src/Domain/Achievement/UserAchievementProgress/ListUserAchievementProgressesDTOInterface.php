<?php

namespace App\Domain\Achievement\UserAchievementProgress;

interface ListUserAchievementProgressesDTOInterface
{
    public function getPage(): int;

    public function getLimit(): int;
}
