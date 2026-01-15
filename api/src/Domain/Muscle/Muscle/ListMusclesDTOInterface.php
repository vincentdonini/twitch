<?php

namespace App\Domain\Muscle\Muscle;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface ListMusclesDTOInterface
{
    public function getPage(): int;
    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}


