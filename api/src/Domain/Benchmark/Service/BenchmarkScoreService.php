<?php

namespace App\Domain\Benchmark\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Benchmark\DTO\BenchmarkScoreDTO;
use App\Domain\User\Service\UserService;
use App\Domain\Benchmark\Entity\BenchmarkScore;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;

final readonly class BenchmarkScoreService
{
    use LocaleTrait;

    public function __construct(
        private UserService      $userService,
        private BenchmarkService $benchmarkService,
    ) {
    }

    public function transformToDTO(BenchmarkScore $benchmarkScore, FilterCollection $filters = null): BenchmarkScoreDTO
    {
        $user = $this->userService->transformToDTO(
            $benchmarkScore->getUser(),
            $filters
        );

        $benchmark = $this->benchmarkService->transformToDTO(
            $benchmarkScore->getBenchmark(),
            $filters
        );

        return new BenchmarkScoreDTO(
            id         : $benchmarkScore->getId(),
            user       : $user,
            benchmark  : $benchmark,
            time       : $benchmarkScore->getTime(),
            repetitions: $benchmarkScore->getRepetitions(),
            weight     : $benchmarkScore->getWeight(),
            performedAt: $benchmarkScore->getPerformedAt(),
            notes      : $benchmarkScore->getNotes(),
            private    : $benchmarkScore->isPrivate()
        );
    }

    public function transformCollectionToDTO(array $benchmarkScores, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $benchmarkScores,
            fn(BenchmarkScore $benchmarkScore) => $this->transformToDTO($benchmarkScore, $filters)
        );
    }
}