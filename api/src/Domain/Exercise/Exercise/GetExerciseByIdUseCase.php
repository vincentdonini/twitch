<?php

namespace App\Domain\Exercise\Exercise;

use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Exercise\Ports\ExerciseDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetExerciseByIdUseCase
{
    public function __construct(
        private readonly ExerciseDALInterface $exerciseDAL,
    ) {
    }

    public function execute(GetExerciseByIdDTOInterface $dto): Exercise
    {
        $exercise = $this->exerciseDAL->getById($dto->getId());
        if (!$exercise instanceof Exercise) {
            throw new EntityNotFoundException();
        }

        return $exercise;
    }
}

