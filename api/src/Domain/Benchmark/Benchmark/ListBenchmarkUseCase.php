<?php

namespace App\Domain\Benchmark\Benchmark;

use App\Domain\Benchmark\Ports\BenchmarkDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListBenchmarkUseCase
{
    public function __construct(
        private BenchmarkDALInterface $benchmarkDAL,
    ) {
    }

    public function execute(ListBenchmarkDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->benchmarkDAL->listBenchmarks(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}

