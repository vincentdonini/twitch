<?php

namespace App\Domain\Benchmark\Benchmark;

use App\Domain\Benchmark\Entity\Benchmark;
use App\Domain\Benchmark\Ports\BenchmarkDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetBenchmarkByIdUseCase
{
    public function __construct(
        private readonly BenchmarkDALInterface $benchmarkDAL,
    ) {

    }

    public function execute(GetBenchmarkByIdDTOInterface $dto): Benchmark
    {
        $benchmark = $this->benchmarkDAL->getById($dto->getId());
        if (!$benchmark instanceof Benchmark) {
            throw new EntityNotFoundException();
        }

        return $benchmark;
    }
}

