<?php

namespace App\UI\Adapters\Http\Muscle\MuscleArea;

use App\Domain\Muscle\MuscleArea\GetMuscleGroupsByMuscleAreaIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetMuscleGroupsByMuscleAreaIdHttp implements GetMuscleGroupsByMuscleAreaIdDTOInterface
{
    public function __construct(
        private Uuid $muscleAreaId,
    ) {
    }

    public function getMuscleAreaId(): Uuid
    {
        return $this->muscleAreaId;
    }
}
