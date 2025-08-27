<?php

namespace App\Infrastructure\Translator\Muscle;

use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Service\MuscleAreaTranslatorInterface;
use App\Shared\Utils\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MuscleAreaTranslator implements MuscleAreaTranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function getName(MuscleArea $muscleArea): string
    {
        return $this->translator->trans('muscle.area.' . StringHelper::kebabToSnake($muscleArea->getSlug()) . '.name');
    }

    public function getDescription(MuscleArea $muscleArea): string
    {
        return $this->translator->trans('muscle.area.' . StringHelper::kebabToSnake($muscleArea->getSlug()) . '.description');
    }
}
