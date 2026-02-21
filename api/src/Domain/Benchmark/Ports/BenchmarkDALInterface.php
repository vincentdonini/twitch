<?php

namespace App\Domain\Benchmark\Ports;

use App\Domain\Benchmark\Entity\Benchmark;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface BenchmarkDALInterface
{
    public function getById(Uuid $id): ?Benchmark;

    public function listBenchmarks(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;

    public function countBenchmarks(): int;
}
