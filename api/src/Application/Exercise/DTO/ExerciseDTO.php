<?php

namespace App\Application\Exercise\DTO;

use App\Application\DTO\BaseDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class ExerciseDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::EXERCISE_LIST, FrontGroupsEnum::EXERCISE_DETAIL,
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public int $id;

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

    public function __construct(
        int    $id,
        string $slug,
        string $title,
        string $summary,
        string $details
    ) {
        parent::__construct($id);

        $this->slug    = $slug;
        $this->title   = $title;
        $this->summary = $summary;
        $this->details = $details;
    }
}
