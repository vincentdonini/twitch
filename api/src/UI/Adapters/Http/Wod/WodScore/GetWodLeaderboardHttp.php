<?php

namespace App\UI\Adapters\Http\Wod\WodScore;

use App\Domain\Wod\Wod\GetWodLeaderboardDTOInterface;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;

final readonly class GetWodLeaderboardHttp implements GetWodLeaderboardDTOInterface
{
    public function __construct(
        private string            $wodId,
        private ?string           $wodDivisionId = null,
        private ?string           $gender = null,
        private ?string           $metric = null,
        private ?int              $page = null,
        private ?int              $limit = null,
        private ?FilterCollection $filters = null,
    ) {
    }

    public function getWodId(): string
    {
        return $this->wodId;
    }

    public function getWodDivisionId(): ?string
    {
        return $this->wodDivisionId;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getMetric(): ?string
    {
        return $this->metric;
    }


    public function getPage(): int
    {
        return $this->page ?? 1;
    }

    public function getLimit(): int
    {
        return $this->limit ?? RequestPaginator::DEFAULT_LIMIT;
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
