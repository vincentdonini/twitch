<?php

namespace App\Domain\Muscle\Ports;

use App\Domain\Muscle\Entity\MuscleGroup;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;

interface MuscleGroupDALInterface
{
    public function getById(string $id): ?MuscleGroup;

    public function listMuscleGroups(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;

    public function getByMuscleAreaId(string $muscleAreaId): array;
}
