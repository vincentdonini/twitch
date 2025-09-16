<?php

namespace App\Domain\Muscle\Service;

use App\Domain\Muscle\Entity\Muscle;

class MuscleService
{
    public function __construct(
        private readonly MuscleTranslatorInterface $muscleTranslator,
        private readonly MuscleGroupService        $muscleGroupService,
        private readonly MuscleAreaService         $muscleAreaService,
    ) {
    }

    public function enrichEntity(Muscle $muscle): Muscle
    {
        $muscle->setName($this->muscleTranslator->getName($muscle));
        $muscle->setDescription($this->muscleTranslator->getDescription($muscle));

        // Area
        $muscle->setArea($this->muscleAreaService->enrichEntity($muscle->getArea()));

        // Group
        $group = $muscle->getGroup();
        if ($group !== null) {
            $muscle->setGroup($this->muscleGroupService->enrichEntity($group));
        }

        return $muscle;
    }
}
