<?php

namespace App\Domain\Muscle\MuscleGroup;

use Symfony\Component\Uid\Uuid;

interface GetMusclesByMuscleGroupIdDTOInterface
{
    public function getMuscleGroupId(): Uuid;
}
