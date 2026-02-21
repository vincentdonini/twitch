<?php

namespace App\Application\Achievement\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class AchievementDTO
{
    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
    ])]
    public string $code;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
    ])]
    public int $position;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
    ])]
    public string $description;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
    ])]
    public AchievementCategoryDTO $category;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
    ])]
    public AchievementGroupDTO $group;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_LIST, FrontGroupsEnum::ACHIEVEMENT_DETAIL,
    ])]
    public array $levels;

    public function __construct(
        Uuid                   $id,
        string                 $code,
        int                    $position,
        string                 $title,
        string                 $description,
        AchievementCategoryDTO $category,
        AchievementGroupDTO    $group,
        array                  $levels,
    ) {
        $this->id          = $id;
        $this->code        = $code;
        $this->position    = $position;
        $this->title       = $title;
        $this->description = $description;
        $this->category    = $category;
        $this->group       = $group;
        $this->levels      = $levels;
    }
}
