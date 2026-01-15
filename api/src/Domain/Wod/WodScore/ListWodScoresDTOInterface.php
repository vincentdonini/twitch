<?php

namespace App\Domain\Wod\WodScore;

use App\Infrastructure\Filters\FilterCollection;

interface ListWodScoresDTOInterface
{
    public function getPage(): int;
    public function getLimit(): int;
    public function getFilters(): ?FilterCollection;
}


