<?php

namespace App\Domain\Exercise\ExerciseCategory;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;

interface ListExerciseCategoryDTOInterface
{
    public function getPage(): int;
    public function getLimit(): int;

    public function getFilters(): ?FilterCollection;

    public function getSorts(): ?SortCollection;
}

