<?php

namespace App\Domain\Achievement\Ports;

use App\Domain\Achievement\Entity\AchievementGroup;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface AchievementGroupDALInterface
{
    public function getById(Uuid $id): ?AchievementGroup;

    public function listAchievementGroups(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;
}
