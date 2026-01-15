<?php

namespace App\Application\Wod\DTO;

use App\Application\DTO\BaseDTO;
use App\Application\Exercise\DTO\ExerciseDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class WodVariantExerciseDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public int $id;

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
        int         $id,
        int         $position,
        ExerciseDTO $exercise,
        array       $metrics
    ) {
        parent::__construct($id);

        $this->position = $position;
        $this->exercise = $exercise;
        $this->metrics  = $metrics;
    }
}
