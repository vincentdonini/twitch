<?php

namespace App\Domain\Muscle\MuscleArea;

use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Ports\MuscleAreaDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetMuscleAreaByIdUseCase
{
    public function __construct(
        private readonly MuscleAreaDALInterface $muscleAreaDAL,
    ) {
    }

    public function execute(GetMuscleAreaByIdDTOInterface $dto): MuscleArea
    {
        $muscleArea = $this->muscleAreaDAL->getById($dto->getId());
        if (!$muscleArea instanceof MuscleArea) {
            throw new EntityNotFoundException();
        }

        return $muscleArea;
    }
}

