<?php

namespace App\UI\Adapters\Http\Muscle\Muscle;

use App\Domain\Muscle\Muscle\GetMuscleByIdDTOInterface;

class GetMuscleByIdHttp implements GetMuscleByIdDTOInterface
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
