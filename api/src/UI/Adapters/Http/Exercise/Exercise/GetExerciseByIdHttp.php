<?php

namespace App\UI\Adapters\Http\Exercise\Exercise;


use App\Domain\Exercise\Exercise\GetExerciseByIdDTOInterface;

class GetExerciseByIdHttp implements GetExerciseByIdDTOInterface
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
