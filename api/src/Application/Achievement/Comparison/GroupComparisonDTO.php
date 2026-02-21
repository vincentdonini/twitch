<?php

namespace App\Application\Achievement\Comparison;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class GroupComparisonDTO
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
    public array $achievements;

    /**
     * @param AchievementComparisonDTO[] $achievements
     */
    public function __construct(
        Uuid   $id,
        string $code,
        int    $position,
        array  $achievements,
    ) {

        $this->id           = $id;
        $this->code         = $code;
        $this->position     = $position;
        $this->achievements = $achievements;
    }
}
