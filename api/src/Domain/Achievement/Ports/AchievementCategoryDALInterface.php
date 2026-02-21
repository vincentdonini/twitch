<?php

namespace App\Domain\Achievement\Ports;

use App\Domain\Achievement\Entity\AchievementCategory;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface AchievementCategoryDALInterface
{
    public function getById(Uuid $id): ?AchievementCategory;

    public function listAchievementCategories(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;
}
