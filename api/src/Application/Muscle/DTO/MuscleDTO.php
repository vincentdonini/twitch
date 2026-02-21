<?php

namespace App\Application\Muscle\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class MuscleDTO
{
    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::MUSCLE_DETAIL,
    ])]
    public string $details;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
    ])]
    public MuscleAreaDTO $area;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
    ])]
    public ?MuscleGroupDTO $group = null;

    public function __construct(
        Uuid            $id,
        string          $slug,
        string          $title,
        string          $summary,
        string          $details,
        MuscleAreaDTO   $area,
        ?MuscleGroupDTO $group = null,
    ) {
        $this->id      = $id;
        $this->slug    = $slug;
        $this->title   = $title;
        $this->summary = $summary;
        $this->details = $details;
        $this->area    = $area;
        $this->group   = $group;
    }
}
