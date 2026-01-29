<?php

namespace App\Application\Benchmark\DTO;

use App\Application\DTO\BaseDTO;
use App\Application\Exercise\DTO\ExerciseDTO;
use App\Domain\Benchmark\Enum\TypeEnum;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class BenchmarkDTO
{
    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public string $name;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_LIST, FrontGroupsEnum::BENCHMARK_DETAIL,
    ])]
    public TypeEnum $type;

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
        Uuid        $id,
        string      $slug,
        string      $name,
        TypeEnum    $type,
        string      $title,
        string      $summary,
        string      $details,
        string      $rules,
        string      $tips,
        ExerciseDTO $exercise,
    ) {
        $this->id       = $id;
        $this->slug     = $slug;
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
