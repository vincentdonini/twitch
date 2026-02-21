<?php

namespace App\UI\Adapters\Http\Muscle\MuscleArea;

use App\Domain\Muscle\MuscleArea\GetMuscleAreaByIdDTOInterface;

final readonly class GetMuscleAreaByIdHttp implements GetMuscleAreaByIdDTOInterface
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
