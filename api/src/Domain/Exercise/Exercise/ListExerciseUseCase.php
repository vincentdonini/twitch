<?php

namespace App\Domain\Exercise\Exercise;

use App\Domain\Exercise\Ports\ExerciseDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListExerciseUseCase
{
    public function __construct(
        private ExerciseDALInterface $exerciseDAL,
    ) {
    }

    public function execute(ListExerciseDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->exerciseDAL->listExercises(
            page   : $dto->getPage(),
            limit  : $dto->getLimit(),
            filters: $dto->getFilters(),
            sorts  : $dto->getSorts(),
        );
    }
}
