<?php

namespace App\UI\Adapters\Http\Muscle\MuscleGroup;

use App\Domain\Muscle\MuscleGroup\GetMusclesByMuscleGroupIdDTOInterface;

class GetMusclesByMuscleGroupIdHttp implements GetMusclesByMuscleGroupIdDTOInterface
{
    public function __construct(
        private readonly string $muscleGroupId,
    ) {

    }

    public function getMuscleGroupId(): string
    {
        return $this->muscleGroupId;
    }
}
