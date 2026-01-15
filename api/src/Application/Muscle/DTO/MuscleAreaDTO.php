<?php

namespace App\Application\Muscle\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class MuscleAreaDTO
{
    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::MUSCLE_AREA_LIST, FrontGroupsEnum::MUSCLE_AREA_DETAIL,
    ])]
    public int $id;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::MUSCLE_AREA_LIST, FrontGroupsEnum::MUSCLE_AREA_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::MUSCLE_AREA_LIST, FrontGroupsEnum::MUSCLE_AREA_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::MUSCLE_AREA_LIST, FrontGroupsEnum::MUSCLE_AREA_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::MUSCLE_AREA_DETAIL,
    ])]
    public string $details;

    public function __construct(
        int    $id,
        string $slug,
        string $title,
        string $summary,
        string $details
    ) {
        $this->id      = $id;
        $this->slug    = $slug;
        $this->title   = $title;
        $this->summary = $summary;
        $this->details = $details;
    }
}
