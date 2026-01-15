<?php

namespace App\UI\Adapters\Http\Wod\WodDivision;

use App\Domain\Wod\WodDivision\ListWodDivisionsDTOInterface;

class ListWodDivisionsHttp implements ListWodDivisionsDTOInterface
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
