<?php

namespace App\Domain\Achievement\UserAchievementProgress;

use App\Domain\Achievement\Enum\AchievementComparisonStatusEnum;

final class ComparisonStats
{
    public int $common    = 0;
    public int $userOnly  = 0;
    public int $otherOnly = 0;

    public int $userUnlocked  = 0;
    public int $otherUnlocked = 0;

    public int $totalScoreUser  = 0;
    public int $totalScoreOther = 0;

    public int $duelScoreUser  = 0;
    public int $duelScoreOther = 0;

    public function update(
        int                             $userProgress,
        int                             $otherProgress,
        AchievementComparisonStatusEnum $status
    ): void {
        if ($userProgress > 0) {
            $this->userUnlocked++;
            $this->totalScoreUser += $userProgress;
        }

        if ($otherProgress > 0) {
            $this->otherUnlocked++;
            $this->totalScoreOther += $otherProgress;
        }

        if ($userProgress > 0 && $otherProgress > 0) {
            $this->duelScoreUser  += $userProgress;
            $this->duelScoreOther += $otherProgress;
        }

        if ($status->isCommon()) $this->common++;
        if ($status->isUserOnly()) $this->userOnly++;
        if ($status->isOtherOnly()) $this->otherOnly++;
    }
}
