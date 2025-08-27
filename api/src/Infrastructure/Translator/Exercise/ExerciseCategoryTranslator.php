<?php

namespace App\Infrastructure\Translator\Exercise;

use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Domain\Exercise\Service\ExerciseCategoryTranslatorInterface;
use App\Shared\Utils\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExerciseCategoryTranslator implements ExerciseCategoryTranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function getName(ExerciseCategory $exerciseCategory): string
    {
        return $this->translator->trans('exercise.category.' . StringHelper::kebabToSnake($exerciseCategory->getSlug()) . '.name');
    }

    public function getDescription(ExerciseCategory $exerciseCategory): string
    {
        return $this->translator->trans('exercise.category.' . StringHelper::kebabToSnake($exerciseCategory->getSlug()) . '.description');
    }
}
