<?php

namespace App\Domain\Exercise\Ports;

use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;

interface ExerciseCategoryDALInterface
{
    public function getById(string $id): ?ExerciseCategory;

    public function listExerciseCategories(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;
}

