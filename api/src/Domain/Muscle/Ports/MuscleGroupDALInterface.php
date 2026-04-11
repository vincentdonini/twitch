<?php

namespace App\Domain\Muscle\Ports;

use App\Domain\Muscle\Entity\MuscleGroup;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface MuscleGroupDALInterface
{
    public function getById(Uuid $id): ?MuscleGroup;

    public function listMuscleGroups(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;

    /** @return MuscleGroup[] */
    public function getByMuscleAreaId(Uuid $muscleAreaId): array;

    /** @return array<string, int> */
    public function getWodCounts(): array;
}
