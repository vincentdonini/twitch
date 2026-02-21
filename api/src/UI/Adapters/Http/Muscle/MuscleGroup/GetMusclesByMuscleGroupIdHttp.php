<?php

namespace App\UI\Adapters\Http\Muscle\MuscleGroup;

use App\Domain\Muscle\MuscleGroup\GetMusclesByMuscleGroupIdDTOInterface;

final readonly class GetMusclesByMuscleGroupIdHttp implements GetMusclesByMuscleGroupIdDTOInterface
{
    public function __construct(
        private string $muscleGroupId,
    ) {

    }

    public function getMuscleGroupId(): string
    {
        return $this->muscleGroupId;
    }
}
