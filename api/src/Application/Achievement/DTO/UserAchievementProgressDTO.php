<?php

namespace App\Application\Achievement\DTO;

use App\Domain\Achievement\Enum\AchievementLevelEnum;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class UserAchievementProgressDTO
{
    #[Groups([
        FrontGroupsEnum::USER_ME,
    ])]
    public Uuid $achievementId;

    #[Groups([
        FrontGroupsEnum::USER_ME,
    ])]
    public string $achievementCode;

    #[Groups([
        FrontGroupsEnum::USER_ME,
    ])]
    public AchievementLevelEnum $achievementLevel;

    #[Groups([
        FrontGroupsEnum::USER_ME,
    ])]
    public float $progress;

    #[Groups([
        FrontGroupsEnum::USER_ME,
    ])]
    public bool $completed;

    #[Groups([
        FrontGroupsEnum::USER_ME,
    ])]
    public ?\DateTimeImmutable $completedAt;

    public function __construct(
        Uuid                 $achievementId,
        string               $achievementCode,
        AchievementLevelEnum $achievementLevel,
        float                $progress,
        bool                 $completed,
        ?\DateTimeImmutable  $completedAt,
    ) {
        $this->achievementId    = $achievementId;
        $this->achievementCode  = $achievementCode;
        $this->achievementLevel = $achievementLevel;
        $this->progress         = $progress;
        $this->completed        = $completed;
        $this->completedAt      = $completedAt;
    }
}
