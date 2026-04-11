<?php

namespace App\Application\Muscle\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class MuscleSegmentDTO
{
    #[Groups([
        FrontGroupsEnum::MUSCLE_SEGMENT_LIST, FrontGroupsEnum::MUSCLE_SEGMENT_DETAIL,
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::MUSCLE_SEGMENT_LIST, FrontGroupsEnum::MUSCLE_SEGMENT_DETAIL,
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::MUSCLE_SEGMENT_LIST, FrontGroupsEnum::MUSCLE_SEGMENT_DETAIL,
        FrontGroupsEnum::MUSCLE_LIST, FrontGroupsEnum::MUSCLE_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::MUSCLE_SEGMENT_LIST, FrontGroupsEnum::MUSCLE_SEGMENT_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::MUSCLE_SEGMENT_DETAIL,
    ])]
    public ?string $details;

    #[Groups([
        FrontGroupsEnum::MUSCLE_SEGMENT_LIST, FrontGroupsEnum::MUSCLE_SEGMENT_DETAIL,
    ])]
    public MuscleDTO $muscle;

    #[Groups([
        FrontGroupsEnum::MUSCLE_SEGMENT_LIST, FrontGroupsEnum::MUSCLE_SEGMENT_DETAIL,
    ])]
    public int $wodCount = 0;

    public function __construct(
        Uuid      $id,
        string    $slug,
        string    $title,
        string    $summary,
        ?string   $details,
        MuscleDTO $muscle,
        int       $wodCount = 0,
    ) {
        $this->id       = $id;
        $this->slug     = $slug;
        $this->title    = $title;
        $this->summary  = $summary;
        $this->details  = $details;
        $this->muscle   = $muscle;
        $this->wodCount = $wodCount;
    }
}
