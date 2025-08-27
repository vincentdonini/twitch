<?php

namespace App\Domain\Muscle\Service;

use App\Application\Muscle\DTO\MuscleAreaDTO;
use App\Application\Muscle\DTO\MuscleGroupDTO;
use App\Domain\Muscle\Entity\MuscleGroup;

class MuscleGroupService
{
    public function __construct(
        private readonly MuscleGroupTranslatorInterface $muscleGroupTranslator,
        private readonly MuscleAreaTranslatorInterface $muscleAreaTranslator,
    ) {

    }

    public function transformToDTO(MuscleGroup $muscleGroup): MuscleGroupDTO
    {
        $muscleArea    = $muscleGroup->getArea();
        $muscleAreaDTO = new MuscleAreaDTO(
            $muscleArea->getId(),
            $muscleArea->getSlug(),
            $this->muscleAreaTranslator->getName($muscleArea),
            $this->muscleAreaTranslator->getDescription($muscleArea),
        );

        return new MuscleGroupDTO(
            $muscleGroup->getId(),
            $muscleGroup->getSlug(),
            $this->muscleGroupTranslator->getName($muscleGroup),
            $this->muscleGroupTranslator->getDescription($muscleGroup),
            $muscleAreaDTO,
        );
    }
}
