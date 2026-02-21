<?php

namespace App\Application\Achievement\Comparison;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

class AchievementComparisonDTO
{
    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public string $code;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public int $position;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public int $userProgress;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public int $otherProgress;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public string $status;


    public function __construct(
        Uuid   $id,
        string $code,
        int    $position,
        int    $userProgress,
        int    $otherProgress,
        string $status,
    ) {
        $this->id            = $id;
        $this->code          = $code;
        $this->position      = $position;
        $this->userProgress  = $userProgress;
        $this->otherProgress = $otherProgress;
        $this->status        = $status;
    }
}
