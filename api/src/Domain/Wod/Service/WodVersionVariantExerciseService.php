<?php

namespace App\Domain\Wod\Service;

use App\Application\Wod\DTO\WodVersionVariantExerciseDTO;
use App\Domain\Wod\Entity\WodVersionVariantExercise;

final class WodVersionVariantExerciseService
{
    public function transformToDTO(WodVersionVariantExercise $wodVersionVariantExercise): WodVersionVariantExerciseDTO
    {
        return new WodVersionVariantExerciseDTO(
            $wodVersionVariantExercise->getId(),
            $wodVersionVariantExercise->getPosition(),
            $wodVersionVariantExercise->getReps(),
            $wodVersionVariantExercise->getWeight(),
            $wodVersionVariantExercise->getWodVersionVariant()->getId(),
            $wodVersionVariantExercise->getExercise()->getId(),
        );
    }
}
