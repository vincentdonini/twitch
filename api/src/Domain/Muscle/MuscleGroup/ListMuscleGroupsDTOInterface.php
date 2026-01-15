<?php

namespace App\Domain\Muscle\MuscleGroup;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface ListMuscleGroupsDTOInterface
{
    public function getPage(): int;
    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}
