<?php

namespace App\Domain\Equipment\Service;

use App\Domain\Equipment\Entity\Equipment;

class EquipmentService
{
    public function __construct(
        private readonly EquipmentTranslatorInterface $equipmentTranslator,
    ) {
    }

    public function enrichEntity(Equipment $equipment): Equipment
    {
        $equipment->setName($this->equipmentTranslator->getName($equipment));
        $equipment->setDescription($this->equipmentTranslator->getDescription($equipment));

        return $equipment;
    }
}
