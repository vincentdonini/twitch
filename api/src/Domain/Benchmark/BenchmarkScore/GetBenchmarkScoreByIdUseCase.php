<?php

namespace App\Domain\Benchmark\BenchmarkScore;

use App\Domain\Benchmark\Entity\BenchmarkScore;
use App\Domain\Benchmark\Ports\BenchmarkScoreDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetBenchmarkScoreByIdUseCase
{
    public function __construct(
        private readonly BenchmarkScoreDALInterface $benchmarkScoreDAL,
    ) {

    }

    public function execute(GetBenchmarkScoreByIdDTOInterface $dto): BenchmarkScore
    {
        $benchmarkScore = $this->benchmarkScoreDAL->getById($dto->getId());
        if (!$benchmarkScore instanceof BenchmarkScore) {
            throw new EntityNotFoundException();
        }

        return $benchmarkScore;
    }
}

