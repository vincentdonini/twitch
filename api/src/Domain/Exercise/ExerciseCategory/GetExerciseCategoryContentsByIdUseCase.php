<?php

namespace App\Domain\Exercise\ExerciseCategory;

use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Domain\Exercise\Ports\ExerciseCategoryDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

readonly class GetExerciseCategoryContentsByIdUseCase
{
    public function __construct(
        private ExerciseCategoryDALInterface $exerciseCategoryDAL,
    ) {

    }

    public function execute(GetExerciseCategoryByIdDTOInterface $dto): Collection
    {
        $exerciseCategory = $this->exerciseCategoryDAL->getById($dto->getId());
        if (!$exerciseCategory instanceof ExerciseCategory) {
            throw new EntityNotFoundException();
        }

        return $exerciseCategory->getContents();
    }
}

