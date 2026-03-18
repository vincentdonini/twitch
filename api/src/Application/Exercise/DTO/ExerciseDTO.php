<?php

namespace App\Application\Exercise\DTO;

use App\Application\Equipment\DTO\EquipmentDTO;
use App\Application\Exercise\DTO\ExerciseCategoryDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class ExerciseDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
    ])]
    public string $details;

    #[Groups([
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
    ])]
    public int $wodCount;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
    ])]
    public ?EquipmentDTO $equipment;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
    ])]
    public ?ExerciseCategoryDTO $exerciseCategory;

    public function __construct(
        Uuid                 $id,
        string               $slug,
        string               $title,
        string               $summary,
        string               $details,
        int                  $wodCount = 0,
        ?EquipmentDTO        $equipment = null,
        ?ExerciseCategoryDTO $exerciseCategory = null,
    ) {
        $this->id               = $id;
        $this->slug             = $slug;
        $this->title            = $title;
        $this->summary          = $summary;
        $this->details          = $details;
        $this->wodCount         = $wodCount;
        $this->equipment        = $equipment;
        $this->exerciseCategory = $exerciseCategory;
    }
}
