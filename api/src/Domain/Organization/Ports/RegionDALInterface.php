<?php

namespace App\Domain\Organization\Ports;

use App\Domain\Geo\Entity\Region;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface RegionDALInterface
{
    public function getById(string $id): ?Region;

    public function listRegions(
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;
}
