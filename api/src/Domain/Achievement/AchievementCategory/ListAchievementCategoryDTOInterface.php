<?php

namespace App\Domain\Achievement\AchievementCategory;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface ListAchievementCategoryDTOInterface
{
    public function getPage(): int;

    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}
