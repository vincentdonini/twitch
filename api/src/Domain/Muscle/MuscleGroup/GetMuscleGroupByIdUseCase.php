<?php

namespace App\Domain\Muscle\MuscleGroup;

use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Ports\MuscleGroupDALInterface;
use Doctrine\ORM\EntityNotFoundException;

class GetMuscleGroupByIdUseCase
{
    public function __construct(
        private readonly MuscleGroupDALInterface $muscleGroupDAL,
    )
    {

    }

    public function execute(GetMuscleGroupByIdDTOInterface $dto): MuscleGroup
    {
        $muscleGroup = $this->muscleGroupDAL->getById($dto->getId());
        if(!$muscleGroup instanceof MuscleGroup){
            throw new EntityNotFoundException();
        }

        return $muscleGroup;
    }
}

