<?php

namespace App\Domain\Achievement\AchievementGroup;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface ListAchievementGroupDTOInterface
{
    public function getPage(): int;

    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}
