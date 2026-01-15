<?php

namespace App\UI\Adapters\Http\Muscle\MuscleArea;

use App\Domain\Muscle\MuscleArea\GetMuscleAreaByIdDTOInterface;

class GetMuscleAreaByIdHttp implements GetMuscleAreaByIdDTOInterface
{
    public function __construct(
        private readonly string $id,
    ) {

    }

    public function getId(): string
    {
        return $this->id;
    }
}
