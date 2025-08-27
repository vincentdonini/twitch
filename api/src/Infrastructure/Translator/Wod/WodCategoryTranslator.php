<?php

namespace App\Infrastructure\Translator\Wod;

use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Service\WodCategoryTranslatorInterface;
use App\Shared\Utils\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class WodCategoryTranslator implements WodCategoryTranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function getName(WodCategory $wodCategory): string
    {
        return $this->translator->trans('wod.category.' . StringHelper::kebabToSnake($wodCategory->getSlug()) . '.name');
    }

    public function getDescription(WodCategory $wodCategory): string
    {
        return $this->translator->trans('wod.category.' . StringHelper::kebabToSnake($wodCategory->getSlug()) . '.description');
    }
}
