<?php

namespace App\Domain\Wod\WodType;

interface ListWodTypesDTOInterface
{
    public function getPage(): int;
    public function getLimit(): int;
    public function getFilters(): ?array;
}

