<?php

namespace App\Domain\Muscle\Ports;

use App\Domain\Muscle\Entity\Muscle;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface MuscleDALInterface
{
    public function getById(string $id): ?Muscle;

    public function listMuscles(
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;
}
