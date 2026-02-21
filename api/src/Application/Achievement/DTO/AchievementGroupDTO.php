<?php

namespace App\Application\Achievement\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class AchievementGroupDTO
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
    public string $code;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
        FrontGroupsEnum::ACHIEVEMENT_GROUP_LIST, FrontGroupsEnum::ACHIEVEMENT_GROUP_DETAIL,
    ])]
    public int $position;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
        FrontGroupsEnum::ACHIEVEMENT_GROUP_LIST, FrontGroupsEnum::ACHIEVEMENT_GROUP_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
        FrontGroupsEnum::ACHIEVEMENT_GROUP_LIST, FrontGroupsEnum::ACHIEVEMENT_GROUP_DETAIL,
    ])]
    public string $description;

    public function __construct(
        Uuid   $id,
        string $code,
        int    $position,
        string $title,
        string $description,
    ) {
        $this->id          = $id;
        $this->code        = $code;
        $this->position    = $position;
        $this->title       = $title;
        $this->description = $description;
    }
}
