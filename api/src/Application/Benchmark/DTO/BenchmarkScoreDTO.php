<?php

namespace App\Application\Benchmark\DTO;

use App\Application\DTO\BaseDTO;
use App\Application\User\DTO\UserDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

class BenchmarkScoreDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public int $id;

    #[Groups([
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public UserDTO $user;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public BenchmarkDTO $benchmark;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public ?int $time;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public ?int $repetitions;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public ?int $weight;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public DateTimeImmutable $performedAt;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public ?string $notes;

    #[Groups([
        FrontGroupsEnum::USER_ME,
        FrontGroupsEnum::BENCHMARK_SCORE_LIST, FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
    ])]
    public bool $private;

    public function __construct(
        int               $id,
        UserDTO           $user,
        BenchmarkDTO      $benchmark,
        ?int              $time,
        ?int              $repetitions,
        ?int              $weight,
        DateTimeImmutable $performedAt,
        ?string           $notes,
        bool              $private,
    ) {
        parent::__construct($id);

        $this->user        = $user;
        $this->benchmark   = $benchmark;
        $this->time        = $time;
        $this->repetitions = $repetitions;
        $this->weight      = $weight;
        $this->performedAt = $performedAt;
        $this->notes       = $notes;
        $this->private     = $private;
    }
}
