<?php

namespace App\Infrastructure\Translator\Wod;

use App\Domain\Wod\Entity\WodVersionType;
use App\Domain\Wod\Service\WodVersionTypeTranslatorInterface;
use App\Shared\Utils\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class WodVersionTypeTranslator implements WodVersionTypeTranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function getName(WodVersionType $wodVersionType): string
    {
        return $this->translator->trans('wod.version_type.' . StringHelper::kebabToSnake($wodVersionType->getSlug()) . '.name');
    }

    public function getDescription(WodVersionType $wodVersionType): string
    {
        return $this->translator->trans('wod.version_type.' . StringHelper::kebabToSnake($wodVersionType->getSlug()) . '.description');
    }
}
