<?php

namespace App\Infrastructure\Paginator;

use Symfony\Component\HttpFoundation\Request;

final class PaginatorValues
{
    public function __construct(
        private int $page,
        private int $limit
    )
    {
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }
}