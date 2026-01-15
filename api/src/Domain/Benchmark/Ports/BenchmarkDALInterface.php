<?php

namespace App\Domain\Benchmark\Ports;

use App\Domain\Benchmark\Entity\Benchmark;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface BenchmarkDALInterface
{
    public function getById(string $id): ?Benchmark;

    public function getByName(string $name): ?Benchmark;

    public function listBenchmarks(
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;
}
