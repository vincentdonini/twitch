<?php

namespace App\Domain\Wod\Ports;

use App\Domain\Wod\Entity\Wod;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface WodDALInterface
{
    public function getById(string $id): ?Wod;

    public function getByName(string $name): ?Wod;

    public function listWods(
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;
}
