<?php

namespace App\UI\Adapters\Http\Exercise\ExerciseCategory;

use App\Domain\Exercise\ExerciseCategory\GetExerciseCategoryByIdDTOInterface;

final readonly class GetExerciseCategoryByIdHttp implements GetExerciseCategoryByIdDTOInterface
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
