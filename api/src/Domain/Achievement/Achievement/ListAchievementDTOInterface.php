<?php

namespace App\Domain\Achievement\Achievement;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface ListAchievementDTOInterface
{
    public function getPage(): int;

    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}
