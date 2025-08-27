<?php

namespace App\Domain\Equipment\Service;

use App\Application\Equipment\DTO\EquipmentDTO;
use App\Domain\Equipment\Entity\Equipment;

class EquipmentService
{
    public function __construct(
        private readonly EquipmentTranslatorInterface $equipmentTranslator,
    ) {

    }

    public function transformToDTO(Equipment $equipment): EquipmentDTO
    {
        return new EquipmentDTO(
            $equipment->getId(),
            $equipment->getSlug(),
            $this->equipmentTranslator->getName($equipment),
            $this->equipmentTranslator->getDescription($equipment),
        );
    }
}
