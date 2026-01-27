<?php

namespace App\Domain\Organization\Ports;

use App\Domain\Geo\Entity\Department;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface DepartmentDALInterface
{
    public function getById(string $id): ?Department;

    public function listDepartments(
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;
}
