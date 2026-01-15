<?php

namespace App\UI\Adapters\Http\Benchmark\BenchmarkScore;

use App\Domain\Benchmark\BenchmarkScore\GetBenchmarkScoreByIdDTOInterface;

readonly class GetBenchmarkScoreByIdHttp implements GetBenchmarkScoreByIdDTOInterface
{
    public function __construct(
        private string $id,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
