<?php

namespace App\UI\Adapters\Http\Achievement\AchievementGroup;

use App\Domain\Achievement\AchievementGroup\ListAchievementGroupDTOInterface;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

final readonly class ListAchievementGroupsHttp implements ListAchievementGroupDTOInterface
{
    public function __construct(
        private ?int              $page = null,
        private ?int              $limit = null,
        private ?FilterCollection $filters = null,
        private ?SortCollection   $sorts = null,
    ) {

    }

    public function getPage(): int
    {
        return $this->page ?? 1;
    }

    public function getLimit(): int
    {
        return $this->limit ?? 15;
    }

    public function getFilters(): ?FilterCollection
    {
        return $this->filters;
    }

    public function getSorts(): ?SortCollection
    {
        return $this->sorts;
    }
}
