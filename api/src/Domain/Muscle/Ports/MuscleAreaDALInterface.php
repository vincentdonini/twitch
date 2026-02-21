<?php

namespace App\Domain\Muscle\Ports;

use App\Domain\Muscle\Entity\MuscleArea;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;

interface MuscleAreaDALInterface
{
    public function getById(string $id): ?MuscleArea;

    public function listMuscleAreas(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;
}
