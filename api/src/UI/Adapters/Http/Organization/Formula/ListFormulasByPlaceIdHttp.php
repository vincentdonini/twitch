<?php

namespace App\UI\Adapters\Http\Organization\Formula;

use App\Domain\Organization\Formula\ListFormulasByPlaceIdDTOInterface;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

final readonly class ListFormulasByPlaceIdHttp implements ListFormulasByPlaceIdDTOInterface
{
    public function __construct(
        private Uuid              $placeId,
        private ?int              $page = null,
        private ?int              $limit = null,
        private ?FilterCollection $filters = null,
        private ?SortCollection   $sorts = null,
    ) {
    }

    public function getPlaceId(): Uuid
    {
        return $this->placeId;
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
