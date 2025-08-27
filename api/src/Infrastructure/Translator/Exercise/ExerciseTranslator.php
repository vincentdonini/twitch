<?php

namespace App\Infrastructure\Translator\Exercise;

use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Exercise\Service\ExerciseTranslatorInterface;
use App\Shared\Utils\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExerciseTranslator implements ExerciseTranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function getName(Exercise $exercise): string
    {
        return $this->translator->trans('exercise.exercise.' . StringHelper::kebabToSnake($exercise->getSlug()) . '.name');
    }

    public function getDescription(Exercise $exercise): string
    {
        return $this->translator->trans('exercise.exercise.' . StringHelper::kebabToSnake($exercise->getSlug()) . '.description');
    }
}
