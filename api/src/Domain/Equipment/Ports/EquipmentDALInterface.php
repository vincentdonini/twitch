<?php

namespace App\Domain\Equipment\Ports;

use App\Domain\Equipment\Entity\Equipment;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface EquipmentDALInterface
{
    public function getById(Uuid $id): ?Equipment;

    /** @return array<string, int> */
    public function getWodCounts(): array;

    public function listEquipments(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;
}
