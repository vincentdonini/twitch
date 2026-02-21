<?php

namespace App\UI\Adapters\Http\Muscle\MuscleGroup;

use App\Domain\Muscle\MuscleGroup\GetMusclesByMuscleGroupIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetMusclesByMuscleGroupIdHttp implements GetMusclesByMuscleGroupIdDTOInterface
{
    public function __construct(
        private Uuid $muscleGroupId,
    ) {
    }

    public function getMuscleGroupId(): Uuid
    {
        return $this->muscleGroupId;
    }
}
