<?php

namespace App\Domain\Exercise\ExerciseCategory;

use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Domain\Exercise\Ports\ExerciseCategoryDALInterface;
use Doctrine\ORM\EntityNotFoundException;

readonly class GetExerciseCategoryByIdUseCase
{
    public function __construct(
        private ExerciseCategoryDALInterface $exerciseCategoryDAL,
    ) {
    }

    public function execute(GetExerciseCategoryByIdDTOInterface $dto): ExerciseCategory
    {
        $exerciseCategory = $this->exerciseCategoryDAL->getById($dto->getId());
        if (!$exerciseCategory instanceof ExerciseCategory) {
            throw new EntityNotFoundException();
        }

        return $exerciseCategory;
    }
}
