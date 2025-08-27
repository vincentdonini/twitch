<?php

namespace App\Domain\Muscle\Service;

use App\Application\Muscle\DTO\MuscleAreaDTO;
use App\Domain\Muscle\Entity\MuscleArea;

class MuscleAreaService
{
    public function __construct(
        private readonly MuscleAreaTranslatorInterface  $muscleAreaTranslator,
    ) {

    }

    public function transformToDTO(MuscleArea $muscleArea): MuscleAreaDTO
    {
        return new MuscleAreaDTO(
            $muscleArea->getId(),
            $muscleArea->getSlug(),
            $this->muscleAreaTranslator->getName($muscleArea),
            $this->muscleAreaTranslator->getDescription($muscleArea),
        );
    }
}
