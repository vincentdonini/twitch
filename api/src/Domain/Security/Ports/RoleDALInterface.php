<?php

namespace App\Domain\Security\Ports;

use App\Domain\Security\Entity\Role;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface RoleDALInterface
{
    public function getById(string $id): ?Role;

    public function listRoles(
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;
}
