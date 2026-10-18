<?php

namespace App\Domain\Benchmark\Ports;

use App\Domain\Benchmark\Entity\BenchmarkScore;
use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface BenchmarkScoreDALInterface
{
    public function getById(string $id): ?BenchmarkScore;

    /* @return BenchmarkScore[] */
    public function getByUser(User $user): array;

    public function listBenchmarkScores(
        int               $page = 1,
        int               $limit = 15,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
        ?User             $user = null,
    ): LightPaginator;
}
