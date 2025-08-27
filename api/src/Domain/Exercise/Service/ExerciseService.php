<?php

namespace App\Domain\Exercise\Service;

use App\Application\Exercise\DTO\ExerciseDTO;
use App\Domain\Exercise\Entity\Exercise;

final class ExerciseService
{
    public function __construct(
        private readonly ExerciseTranslatorInterface $exerciseTranslator,
    ) {

    }

    public function transformToDTO(Exercise $exercise): ExerciseDTO
    {
        return new ExerciseDTO(
            $exercise->getId(),
            $exercise->getSlug(),
            $this->exerciseTranslator->getName($exercise),
            $this->exerciseTranslator->getDescription($exercise),
            $exercise->getEquipment()->getId(),
            $exercise->getExerciseCategory()->getId(),
        );
    }
}
