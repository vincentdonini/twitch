<?php

namespace App\UI\Adapters\Http\Muscle\MuscleArea;

use App\Domain\Muscle\MuscleArea\GetMuscleGroupsByMuscleAreaIdDTOInterface;

final readonly class GetMuscleGroupsByMuscleAreaIdHttp implements GetMuscleGroupsByMuscleAreaIdDTOInterface
{
    public function __construct(
        private string $muscleAreaId,
    ) {

    }

    public function getMuscleAreaId(): string
    {
        return $this->muscleAreaId;
    }
}
