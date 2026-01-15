<?php

namespace App\UI\Adapters\Http\User\User;

use App\Domain\User\User\ListUsersDTOInterface;

class ListUsersHttp implements ListUsersDTOInterface
{
    public function __construct(
        private readonly ?int   $page = null,
        private readonly ?int   $limit = null,
        private readonly ?array $filters = null,
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

    public function getFilters(): ?array
    {
        return $this->filters;
    }
}
