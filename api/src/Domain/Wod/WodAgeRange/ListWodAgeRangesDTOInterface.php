<?php

namespace App\Domain\Wod\WodAgeRange;

interface ListWodAgeRangesDTOInterface
{
    public function getPage(): int;
    public function getLimit(): int;
    public function getFilters(): ?array;
}

