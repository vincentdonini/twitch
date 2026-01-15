<?php

namespace App\Domain\Muscle\MuscleArea;

use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Ports\MuscleAreaDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

class GetMuscleAreaContentsByIdUseCase
{
    public function __construct(
        private readonly MuscleAreaDALInterface $muscleAreaDAL,
    ) {

    }

    public function execute(GetMuscleAreaByIdDTOInterface $dto): Collection
    {
        $muscleArea = $this->muscleAreaDAL->getById($dto->getId());
        if (!$muscleArea instanceof MuscleArea) {
            throw new EntityNotFoundException();
        }

        return $muscleArea->getContents();
    }
}

