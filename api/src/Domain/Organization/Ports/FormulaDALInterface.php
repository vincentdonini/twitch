<?php

namespace App\Domain\Organization\Ports;

use App\Domain\Organization\Entity\Formula;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface FormulaDALInterface
{
    public function getById(Uuid $id): ?Formula;

    public function listFormulas(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;

    public function listFormulasByPlaceId(
        Uuid              $placeId,
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;

    public function hasSubscriptions(Formula $formula): bool;
}
