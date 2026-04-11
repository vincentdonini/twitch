<?php

namespace App\Application\Muscle\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class MuscleDTO
{
    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
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

    /** @var MuscleSegmentDTO[] */
    #[Groups([
        FrontGroupsEnum::MUSCLE_DETAIL,
    ])]
    public array $segments = [];

    #[Groups([
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
    ])]
    public int $wodCount = 0;

    public function __construct(
        Uuid            $id,
        string          $slug,
        string          $title,
        string          $summary,
        string          $details,
        MuscleAreaDTO   $area,
        ?MuscleGroupDTO $group = null,
        array           $segments = [],
        int             $wodCount = 0,
    ) {
        $this->id       = $id;
        $this->slug     = $slug;
        $this->title    = $title;
        $this->summary  = $summary;
        $this->details  = $details;
        $this->area     = $area;
        $this->group    = $group;
        $this->segments = $segments;
        $this->wodCount = $wodCount;
    }
}
