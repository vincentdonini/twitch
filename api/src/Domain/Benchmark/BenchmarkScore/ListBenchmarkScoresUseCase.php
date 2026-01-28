<?php

namespace App\Domain\Benchmark\BenchmarkScore;

use App\Domain\User\Entity\User;
use App\Domain\Benchmark\Ports\BenchmarkScoreDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListBenchmarkScoresUseCase
{
    public function __construct(
        private BenchmarkScoreDALInterface $benchmarkScoreDAL,
    ) {
    }

    public function execute(
        ListBenchmarkScoresDTOInterface $dto,
        ?User                           $user,
    ): LightPaginator {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->benchmarkScoreDAL->listBenchmarkScores(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            user   : $user,
        );
    }
}

