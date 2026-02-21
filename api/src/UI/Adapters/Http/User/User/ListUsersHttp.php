<?php

namespace App\UI\Adapters\Http\User\User;

use App\Domain\User\User\ListUsersDTOInterface;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;

class ListUsersHttp implements ListUsersDTOInterface
{
    public function __construct(
        private readonly ?int     $page = null,
        private readonly ?int     $limit = null,
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
