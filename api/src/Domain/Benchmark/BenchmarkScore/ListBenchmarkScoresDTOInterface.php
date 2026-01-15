<?php

namespace App\Domain\Benchmark\BenchmarkScore;

use App\Infrastructure\Filters\FilterCollection;

interface ListBenchmarkScoresDTOInterface
{
    public function getPage(): int;
    public function getLimit(): int;
    public function getFilters(): ?FilterCollection;
}


