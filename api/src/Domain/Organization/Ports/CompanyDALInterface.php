<?php

namespace App\Domain\Organization\Ports;

use App\Domain\Organization\Entity\Company;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface CompanyDALInterface
{
    public function getById(string $id): ?Company;

    public function listCompanies(
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;
}
