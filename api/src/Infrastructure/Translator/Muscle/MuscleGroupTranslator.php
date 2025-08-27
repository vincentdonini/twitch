<?php

namespace App\Infrastructure\Translator\Muscle;

use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Service\MuscleGroupTranslatorInterface;
use App\Shared\Utils\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MuscleGroupTranslator implements MuscleGroupTranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function getName(MuscleGroup $muscleGroup): string
    {
        return $this->translator->trans('muscle.group.' . StringHelper::kebabToSnake($muscleGroup->getSlug()) . '.name');
    }

    public function getDescription(MuscleGroup $muscleGroup): string
    {
        return $this->translator->trans('muscle.group.' . StringHelper::kebabToSnake($muscleGroup->getSlug()) . '.description');
    }
}
