<?php

namespace App\Domain\Exercise\ExerciseCategory;

use Symfony\Component\Uid\Uuid;

interface GetExerciseCategoryByIdDTOInterface
{
    public function getId(): Uuid;
}
