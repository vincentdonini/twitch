<?php

namespace App\UI\Adapters\Http\Muscle\MuscleArea;

use App\Domain\Muscle\MuscleArea\GetMuscleGroupsByMuscleAreaIdDTOInterface;

class GetMuscleGroupsByMuscleAreaIdHttp implements GetMuscleGroupsByMuscleAreaIdDTOInterface
{
    public function __construct(
        private readonly string $muscleAreaId,
    ) {

    }

    public function getMuscleAreaId(): string
    {
        return $this->muscleAreaId;
    }
}
