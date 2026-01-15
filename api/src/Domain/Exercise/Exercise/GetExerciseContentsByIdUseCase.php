<?php

namespace App\Domain\Exercise\Exercise;

use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Exercise\Ports\ExerciseDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

class GetExerciseContentsByIdUseCase
{
    public function __construct(
        private readonly ExerciseDALInterface $exerciseDAL,
    ) {

    }

    public function execute(GetExerciseByIdDTOInterface $dto): Collection
    {
        $exercise = $this->exerciseDAL->getById($dto->getId());
        if (!$exercise instanceof Exercise) {
            throw new EntityNotFoundException();
        }

        return $exercise->getContents();
    }
}

