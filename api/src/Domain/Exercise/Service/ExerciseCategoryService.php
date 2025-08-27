<?php

namespace App\Domain\Exercise\Service;

use App\Application\Exercise\DTO\ExerciseCategoryDTO;
use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Infrastructure\Translator\Exercise\ExerciseCategoryTranslator;

final class ExerciseCategoryService
{
    public function __construct(
        private readonly ExerciseCategoryTranslator $exerciseCategoryTranslator
    )
    {
    }

    public function transformToDTO(ExerciseCategory $category): ExerciseCategoryDTO
    {
        return new ExerciseCategoryDTO(
            $category->getId(),
            $category->getSlug(),
            $this->exerciseCategoryTranslator->getName($category),
            $this->exerciseCategoryTranslator->getDescription($category)
        );
    }
}
