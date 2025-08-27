<?php

namespace App\Domain\Muscle\Service;

use App\Domain\Muscle\Entity\MuscleGroup;

interface MuscleGroupTranslatorInterface
{
    public function getName(MuscleGroup $muscleGroup): string;
    public function getDescription(MuscleGroup $muscleGroup): string;
}
