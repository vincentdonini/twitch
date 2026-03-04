<?php

namespace App\UI\Adapters\Http\Organization\Subscription;

use App\Domain\Organization\Subscription\ListSubscriptionsByPlaceIdDTOInterface;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

final readonly class ListSubscriptionsByPlaceIdHttp implements ListSubscriptionsByPlaceIdDTOInterface
{
    public function __construct(
        private Uuid              $placeId,
        private Uuid              $formulaId,
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

    public function getFormulaId(): Uuid
    {
        return $this->formulaId;
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
