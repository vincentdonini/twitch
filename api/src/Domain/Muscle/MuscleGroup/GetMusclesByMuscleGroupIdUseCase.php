<?php

namespace App\Domain\Muscle\MuscleGroup;

use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Ports\MuscleDALInterface;
use App\Domain\Muscle\Ports\MuscleGroupDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetMusclesByMuscleGroupIdUseCase
{
    public function __construct(
        private readonly MuscleGroupDALInterface $muscleGroupDAL,
        private readonly MuscleDALInterface      $muscleDAL,
    ) {

    }

    public function execute(GetMusclesByMuscleGroupIdDTOInterface $dto): array
    {
        $muscleGroup = $this->muscleGroupDAL->getById($dto->getMuscleGroupId());
        if (!$muscleGroup instanceof MuscleGroup) {
            throw new EntityNotFoundException();
        }

        return $this->muscleDAL->getByMuscleGroupId($muscleGroup->getId());
    }
}

