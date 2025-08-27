<?php

namespace App\Infrastructure\Translator\Wod;

use App\Domain\Wod\Entity\WodType;
use App\Domain\Wod\Service\WodTypeTranslatorInterface;
use App\Shared\Utils\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class WodTypeTranslator implements WodTypeTranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function getName(WodType $wodType): string
    {
        return $this->translator->trans('wod.type.' . StringHelper::kebabToSnake($wodType->getSlug()) . '.name');
    }

    public function getDescription(WodType $wodType): string
    {
        return $this->translator->trans('wod.type.' . StringHelper::kebabToSnake($wodType->getSlug()) . '.description');
    }
}
