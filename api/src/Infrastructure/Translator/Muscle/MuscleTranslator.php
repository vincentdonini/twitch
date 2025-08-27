<?php

namespace App\Infrastructure\Translator\Muscle;

use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Service\MuscleTranslatorInterface;
use App\Shared\Utils\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MuscleTranslator implements MuscleTranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function getName(Muscle $muscle): string
    {
        return $this->translator->trans('muscle.muscle.' . StringHelper::kebabToSnake($muscle->getSlug()) . '.name');
    }

    public function getDescription(Muscle $muscle): string
    {
        return $this->translator->trans('muscle.muscle.' . StringHelper::kebabToSnake($muscle->getSlug()) . '.description');
    }
}
