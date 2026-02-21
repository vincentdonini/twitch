<?php

namespace App\UI\Adapters\Http\Muscle\MuscleGroup;

use App\Domain\Muscle\MuscleGroup\GetMuscleGroupByIdDTOInterface;

final readonly class GetMuscleGroupByIdHttp implements GetMuscleGroupByIdDTOInterface
{
    public function __construct(
        private string $id,
    ) {

    }

    public function getId(): string
    {
        return $this->id;
    }
}
