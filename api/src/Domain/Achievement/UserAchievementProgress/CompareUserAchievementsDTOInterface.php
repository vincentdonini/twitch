<?php

namespace App\Domain\Achievement\UserAchievementProgress;

use Symfony\Component\Uid\Uuid;

interface CompareUserAchievementsDTOInterface
{
    public function getUserId(): Uuid;

    public function getOtherId(): Uuid;
}
