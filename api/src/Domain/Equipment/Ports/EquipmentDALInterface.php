<?php

namespace App\Domain\Equipment\Ports;

use App\Domain\Equipment\Entity\Equipment;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface EquipmentDALInterface
{
    public function getById(string $id): ?Equipment;

    public function listEquipments(
        int               $page = 1,
        int               $limit = 15,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;
}
