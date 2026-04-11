<?php

namespace App\Application\Muscle\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class MuscleAreaDTO
{
    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::MUSCLE_AREA_LIST, FrontGroupsEnum::MUSCLE_AREA_DETAIL,
        FrontGroupsEnum::MUSCLE_GROUP_LIST, FrontGroupsEnum::MUSCLE_GROUP_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::MUSCLE_AREA_LIST, FrontGroupsEnum::MUSCLE_AREA_DETAIL,
        FrontGroupsEnum::MUSCLE_GROUP_LIST, FrontGroupsEnum::MUSCLE_GROUP_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::MUSCLE_AREA_LIST, FrontGroupsEnum::MUSCLE_AREA_DETAIL,
        FrontGroupsEnum::MUSCLE_GROUP_LIST, FrontGroupsEnum::MUSCLE_GROUP_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::MUSCLE_AREA_LIST, FrontGroupsEnum::MUSCLE_AREA_DETAIL,
        FrontGroupsEnum::MUSCLE_GROUP_LIST, FrontGroupsEnum::MUSCLE_GROUP_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::MUSCLE_AREA_DETAIL,
    ])]
    public string $details;

    #[Groups([
        FrontGroupsEnum::MUSCLE_AREA_LIST, FrontGroupsEnum::MUSCLE_AREA_DETAIL,
    ])]
    public int $wodCount = 0;

    public function __construct(
        Uuid   $id,
        string $slug,
        string $title,
        string $summary,
        string $details,
        int    $wodCount = 0,
    ) {
        $this->id       = $id;
        $this->slug     = $slug;
        $this->title    = $title;
        $this->summary  = $summary;
        $this->details  = $details;
        $this->wodCount = $wodCount;
    }
}
