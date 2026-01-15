<?php

namespace App\Domain\Benchmark\BenchmarkScore;

use App\Domain\User\Entity\User;
use App\Domain\Benchmark\Ports\BenchmarkScoreDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

class ListBenchmarkScoresUseCase
{
    public function __construct(
        private readonly BenchmarkScoreDALInterface $benchmarkScoreDAL,
    ) {
    }

    public function execute(
        ListBenchmarkScoresDTOInterface $dto,
        ?User                           $currentUser,
    ): LightPaginator {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->benchmarkScoreDAL->listBenchmarkScores(
            page       : $dto->getPage(),
            limit      : $dto->getLimit(),
            filters    : $dto->getFilters(),
            currentUser: $currentUser,
        );
    }
}

