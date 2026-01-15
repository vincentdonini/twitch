<?php

namespace App\Domain\Benchmark\Ports;

use App\Domain\User\Entity\User;
use App\Domain\Benchmark\Entity\BenchmarkScore;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;

interface BenchmarkScoreDALInterface
{
    public function getById(string $id): ?BenchmarkScore;

    public function listBenchmarkScores(
        int               $page = 1,
        int               $limit = 15,
        ?FilterCollection $filters = null,
        ?User             $currentUser = null,
    ): LightPaginator;
}
