<?php

namespace App\Application\Wod\DTO;

use App\Application\Exercise\DTO\ExerciseDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class WodVariantExerciseDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public int $position;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public ExerciseDTO $exercise;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public array $metrics;

    public function __construct(
        Uuid        $id,
        int         $position,
        ExerciseDTO $exercise,
        array       $metrics
    ) {
        $this->id       = $id;
        $this->position = $position;
        $this->exercise = $exercise;
        $this->metrics  = $metrics;
    }
}
