<?php

namespace App\Infrastructure\Translator\Movement;

use App\Domain\Movement\Entity\MovementCategory;
use App\Domain\Movement\Service\MovementCategoryTranslatorInterface;
use App\Shared\Utils\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MovementCategoryTranslator implements MovementCategoryTranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function getName(MovementCategory $movementCategory): string
    {
        return $this->translator->trans('category.' . StringHelper::kebabToSnake($movementCategory->getSlug()) . '.name', [], 'movement');
    }

    public function getDescription(MovementCategory $movementCategory): string
    {
        return $this->translator->trans('category.' . StringHelper::kebabToSnake($movementCategory->getSlug()) . '.description', [], 'movement');
    }
}
