<?php

namespace App\Application\Achievement\Comparison;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class SummaryComparisonDTO
{
    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public int $totalAchievements;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public int $commonUnlocked;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public int $userOnly;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public int $otherOnly;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public float $completionRateUser;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public float $completionRateOther;


    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public ScoreComparisonDTO $score;


    public function __construct(
        int                $totalAchievements,
        int                $commonUnlocked,
        int                $userOnly,
        int                $otherOnly,
        float              $completionRateUser,
        float              $completionRateOther,
        ScoreComparisonDTO $score,
    ) {
        $this->totalAchievements   = $totalAchievements;
        $this->commonUnlocked      = $commonUnlocked;
        $this->userOnly            = $userOnly;
        $this->otherOnly           = $otherOnly;
        $this->completionRateUser  = $completionRateUser;
        $this->completionRateOther = $completionRateOther;
        $this->score               = $score;
    }
}
