<?php

namespace App\UI\Adapters\Http\Security\Permission;

use App\Domain\Security\Permission\ListPermissionDTOInterface;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;

final readonly class ListPermissionHttp implements ListPermissionDTOInterface
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
