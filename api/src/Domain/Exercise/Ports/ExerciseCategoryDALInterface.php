<?php

namespace App\Domain\Exercise\Ports;

use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface ExerciseCategoryDALInterface
{
    public function getById(Uuid $id): ?ExerciseCategory;

    /** @return array<string, int> */
    public function getWodCounts(): array;

    public function listExerciseCategories(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;
}
