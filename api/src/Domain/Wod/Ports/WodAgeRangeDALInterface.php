<?php

namespace App\Domain\Wod\Ports;

use App\Domain\Wod\Entity\WodAgeRange;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;

interface WodAgeRangeDALInterface
{
    public function getById(string $id): ?WodAgeRange;

    public function getBySlug(string $slug): ?WodAgeRange;

    public function listWodAgeRanges(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;
}
