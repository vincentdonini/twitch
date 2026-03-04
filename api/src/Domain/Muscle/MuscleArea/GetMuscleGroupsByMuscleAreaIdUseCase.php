<?php

namespace App\Domain\Muscle\MuscleArea;

use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Ports\MuscleAreaDALInterface;
use App\Domain\Muscle\Ports\MuscleGroupDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetMuscleGroupsByMuscleAreaIdUseCase
{
    public function __construct(
        private readonly MuscleAreaDALInterface  $muscleAreaDAL,
        private readonly MuscleGroupDALInterface $muscleGroupDAL,
    ) {
    }

    /** @return MuscleGroup[] */
    public function execute(GetMuscleGroupsByMuscleAreaIdDTOInterface $dto): array
    {
        $muscleArea = $this->muscleAreaDAL->getById($dto->getMuscleAreaId());
        if (!$muscleArea instanceof MuscleArea) {
            throw new EntityNotFoundException();
        }

        return $this->muscleGroupDAL->getByMuscleAreaId($dto->getMuscleAreaId());
    }
}
