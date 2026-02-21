<?php

namespace App\Domain\Muscle\Ports;

use App\Domain\Muscle\Entity\Muscle;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface MuscleDALInterface
{
    public function getById(Uuid $id): ?Muscle;

    public function listMuscles(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;
}
