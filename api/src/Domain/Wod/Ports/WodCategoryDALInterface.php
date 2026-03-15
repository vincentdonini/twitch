<?php

namespace App\Domain\Wod\Ports;

use App\Domain\Wod\Entity\WodCategory;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface WodCategoryDALInterface
{
    public function getById(Uuid $id): ?WodCategory;

    /** @return array<string, int> */
    public function getWodCounts(): array;

    public function listWodCategories(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;
}
