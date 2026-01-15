<?php

namespace App\Domain\Security\Ports;

use App\Domain\Security\Entity\Permission;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface PermissionDALInterface
{
    public function getById(string $id): ?Permission;

    public function listPermissions(
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;

    public function listPermissionsByRole(
        string           $roleId,
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;
}
