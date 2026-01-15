<?php

namespace App\Domain\Box\Ports;

use App\Domain\Box\Entity\Box;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface BoxDALInterface
{
    public function getById(string $id): ?Box;

    public function getByName(string $name): ?Box;

    public function listBoxs(
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;
}
