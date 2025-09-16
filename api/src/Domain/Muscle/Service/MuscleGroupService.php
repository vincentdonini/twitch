<?php

namespace App\Domain\Muscle\Service;

use App\Domain\Muscle\Entity\MuscleGroup;

class MuscleGroupService
{
    public function __construct(
        private readonly MuscleGroupTranslatorInterface $muscleGroupTranslator,
    ) {

    }

    public function enrichEntity(MuscleGroup $muscleGroup): MuscleGroup
    {
        $muscleGroup->setName($this->muscleGroupTranslator->getName($muscleGroup));
        $muscleGroup->setDescription($this->muscleGroupTranslator->getDescription($muscleGroup));

        return $muscleGroup;
    }
}
