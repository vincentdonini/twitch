<?php

namespace App\Domain\Organization\Ports;

use App\Domain\Organization\Entity\Place;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface PlaceDALInterface
{
    public function getById(Uuid $id): ?Place;

    public function listPlaces(
        int              $page = 1,
        int              $limit = 15,
        FilterCollection $filters = null,
        SortCollection   $sorts = null
    ): LightPaginator;
}
