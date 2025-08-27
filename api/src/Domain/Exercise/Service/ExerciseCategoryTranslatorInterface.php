<?php

namespace App\Domain\Exercise\Service;

use App\Domain\Exercise\Entity\ExerciseCategory;

interface ExerciseCategoryTranslatorInterface
{
    public function getName(ExerciseCategory $exerciseCategory): string;
    public function getDescription(ExerciseCategory $exerciseCategory): string;
}
