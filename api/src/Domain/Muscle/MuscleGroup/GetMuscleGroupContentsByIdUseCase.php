<?php

namespace App\Domain\Muscle\MuscleGroup;

use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Ports\MuscleGroupDALInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityNotFoundException;

class GetMuscleGroupContentsByIdUseCase
{
    public function __construct(
        private readonly MuscleGroupDALInterface $muscleGroupDAL,
    ) {

    }

    public function execute(GetMuscleGroupByIdDTOInterface $dto): Collection
    {
        $muscleGroup = $this->muscleGroupDAL->getById($dto->getId());
        if (!$muscleGroup instanceof MuscleGroup) {
            throw new EntityNotFoundException();
        }

        return $muscleGroup->getContents();
    }
}

