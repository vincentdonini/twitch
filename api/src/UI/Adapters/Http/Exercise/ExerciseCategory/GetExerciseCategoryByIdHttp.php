<?php

namespace App\UI\Adapters\Http\Exercise\ExerciseCategory;

use App\Domain\Exercise\ExerciseCategory\GetExerciseCategoryByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetExerciseCategoryByIdHttp implements GetExerciseCategoryByIdDTOInterface
{
    public function __construct(
        private Uuid $id,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
