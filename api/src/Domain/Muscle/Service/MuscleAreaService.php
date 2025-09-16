<?php

namespace App\Domain\Muscle\Service;

use App\Domain\Muscle\Entity\MuscleArea;

class MuscleAreaService
{
    public function __construct(
        private readonly MuscleAreaTranslatorInterface  $muscleAreaTranslator,
    ) {
    }

    public function enrichEntity(MuscleArea $muscleArea): MuscleArea
    {
        $muscleArea->setName($this->muscleAreaTranslator->getName($muscleArea));
        $muscleArea->setDescription($this->muscleAreaTranslator->getDescription($muscleArea));

        return $muscleArea;
    }
}
