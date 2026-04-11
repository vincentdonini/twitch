<?php

namespace App\Domain\Muscle\Ports;

use App\Domain\Muscle\Entity\MuscleSegment;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface MuscleSegmentDALInterface
{
    public function getById(Uuid $id): ?MuscleSegment;

    public function listMuscleSegments(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;

    /** @return MuscleSegment[] */
    public function getByMuscleId(Uuid $muscleId): array;

    /** @return array<string, int> */
    public function getWodCounts(): array;
}
