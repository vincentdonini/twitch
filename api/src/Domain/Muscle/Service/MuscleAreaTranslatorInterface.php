<?php

namespace App\Domain\Muscle\Service;

use App\Domain\Muscle\Entity\MuscleArea;

interface MuscleAreaTranslatorInterface
{
    public function getName(MuscleArea $muscleArea): string;
    public function getDescription(MuscleArea $muscleArea): string;
}
