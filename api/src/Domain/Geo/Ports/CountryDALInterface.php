<?php

namespace App\Domain\Geo\Ports;

use App\Domain\Geo\Entity\Country;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface CountryDALInterface
{
    public function getById(string $id): ?Country;

    public function listCountries(
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;
}
