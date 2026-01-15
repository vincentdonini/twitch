<?php

namespace App\Domain\Exercise\ExerciseCategory;

use App\Domain\Exercise\Ports\ExerciseCategoryDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListExerciseCategoryUseCase
{
    public function __construct(
        private ExerciseCategoryDALInterface $exerciseCategoryDAL,
    ) {
    }

    public function execute(ListExerciseCategoryDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->exerciseCategoryDAL->listExerciseCategories(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}

