<?php

namespace App\Application\Benchmark\DTO;

use App\Application\DTO\BaseDTO;
use App\Application\Exercise\DTO\ExerciseDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class BenchmarkDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public int $id;

    #[Groups([
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public string $name;

    #[Groups([
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public string $type;

    #[Groups([
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public string $details;

    #[Groups([
        FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public string $rules;

    #[Groups([
        FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public string $tips;

    #[Groups([
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public ExerciseDTO $exercise;

    public function __construct(
        int         $id,
        string      $name,
        string      $type,
        string      $title,
        string      $summary,
        string      $details,
        string      $rules,
        string      $tips,
        ExerciseDTO $exercise,
    ) {
        parent::__construct($id);

        $this->name     = $name;
        $this->type     = $type;
        $this->title    = $title;
        $this->summary  = $summary;
        $this->details  = $details;
        $this->rules    = $rules;
        $this->tips     = $tips;
        $this->exercise = $exercise;
    }
}
