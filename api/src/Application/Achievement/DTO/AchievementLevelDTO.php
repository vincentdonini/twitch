<?php

namespace App\Application\Achievement\DTO;

use App\Domain\Achievement\Enum\AchievementLevelEnum;
use App\Domain\Achievement\Enum\AchievementRarityEnum;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class AchievementLevelDTO
{
    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
        FrontGroupsEnum::ACHIEVEMENT_GROUP_LIST, FrontGroupsEnum::ACHIEVEMENT_GROUP_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
        FrontGroupsEnum::ACHIEVEMENT_GROUP_LIST, FrontGroupsEnum::ACHIEVEMENT_GROUP_DETAIL,
    ])]
    public AchievementLevelEnum $level;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
    ])]
    public ?float $unlockRatio;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
    ])]
    public ?AchievementRarityEnum $rarity;

    public function __construct(
        Uuid                   $id,
        AchievementLevelEnum   $level,
        ?float                 $unlockRatio = null,
        ?AchievementRarityEnum $rarity = null,
    ) {
        $this->id          = $id;
        $this->level       = $level;
        $this->unlockRatio = $unlockRatio;
        $this->rarity      = $rarity;
    }
}
