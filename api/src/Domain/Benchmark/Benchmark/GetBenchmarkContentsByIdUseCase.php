<?php

namespace App\Domain\Benchmark\Benchmark;

use App\Domain\Benchmark\Entity\Benchmark;
use App\Domain\Benchmark\Ports\BenchmarkDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

class GetBenchmarkContentsByIdUseCase
{
    public function __construct(
        private readonly BenchmarkDALInterface $benchmarkDAL,
    ) {

    }

    public function execute(GetBenchmarkByIdDTOInterface $dto): Collection
    {
        $benchmark = $this->benchmarkDAL->getById($dto->getId());
        if (!$benchmark instanceof Benchmark) {
            throw new EntityNotFoundException();
        }

        return $benchmark->getContents();
    }
}

