<?php

namespace App\Domain\Wod\WodDivision;

interface ListWodDivisionsDTOInterface
{
    public function getPage(): int;
    public function getLimit(): int;
    public function getFilters(): ?array;
}

