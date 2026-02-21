<?php

namespace App\Domain\Wod\Ports;

use App\Domain\Wod\Entity\Wod;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;

interface WodDALInterface
{
    public function getById(string $id): ?Wod;

    public function getByName(string $name): ?Wod;

    public function listWods(
        int              $page = 1,
        int              $limit = RequestPaginator::DEFAULT_LIMIT,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;

    public function countWods(): int;

    public function countByWodCategorySlug(string $wodCategorySlug): int;

    public function countByName(string $name): int;
}
