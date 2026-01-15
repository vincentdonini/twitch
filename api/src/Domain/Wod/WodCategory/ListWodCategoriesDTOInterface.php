<?php

namespace App\Domain\Wod\WodCategory;

interface ListWodCategoriesDTOInterface
{
    public function getPage(): int;
    public function getLimit(): int;
    public function getFilters(): ?array;
}

