<?php

namespace App\Infrastructure\Translator\Equipment;

use App\Domain\Equipment\Service\EquipmentTranslatorInterface;
use App\Domain\Equipment\Entity\Equipment;
use App\Shared\Utils\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class EquipmentTranslator implements EquipmentTranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function getName(Equipment $equipment): string
    {
        return $this->translator->trans('equipment.' . StringHelper::kebabToSnake($equipment->getSlug()) . '.name');
    }

    public function getDescription(Equipment $equipment): string
    {
        return $this->translator->trans('equipment.' . StringHelper::kebabToSnake($equipment->getSlug()) . '.description');
    }
}
