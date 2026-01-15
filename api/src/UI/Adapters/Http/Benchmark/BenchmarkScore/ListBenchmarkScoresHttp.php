<?php

namespace App\UI\Adapters\Http\Benchmark\BenchmarkScore;

use App\Domain\Benchmark\BenchmarkScore\ListBenchmarkScoresDTOInterface;
use App\Infrastructure\Filters\FilterCollection;

readonly class ListBenchmarkScoresHttp implements ListBenchmarkScoresDTOInterface
{
    public function __construct(
        private ?int              $page = null,
        private ?int              $limit = null,
        private ?FilterCollection $filters = null,
    ) {
    }

    public function getPage(): int
    {
        return $this->page ?? 1;
    }

    public function getLimit(): int
    {
        return $this->limit ?? 15;
    }

    public function getFilters(): ?FilterCollection
    {
        return $this->filters;
    }
}
