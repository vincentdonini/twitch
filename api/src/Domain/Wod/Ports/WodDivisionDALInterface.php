<?php

namespace App\Domain\Wod\Ports;

use App\Domain\Wod\Entity\WodDivision;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface WodDivisionDALInterface
{
    public function getById(Uuid $id): ?WodDivision;

    public function getBySlug(string $slug): ?WodDivision;

    public function listWodDivisions(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;
}
