<?php

namespace App\UI\Adapters\Http\Muscle\Muscle;

use App\Domain\Muscle\Muscle\GetMuscleByIdDTOInterface;

final readonly class GetMuscleByIdHttp implements GetMuscleByIdDTOInterface
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
