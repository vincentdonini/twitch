<?php

namespace App\Domain\Muscle\MuscleArea;

use Symfony\Component\Uid\Uuid;

interface GetMuscleGroupsByMuscleAreaIdDTOInterface
{
    public function getMuscleAreaId(): Uuid;
}
