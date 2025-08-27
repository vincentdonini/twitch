<?php

namespace App\Domain\Equipment\Service;

use App\Domain\Equipment\Entity\Equipment;

interface EquipmentTranslatorInterface
{
    public function getName(Equipment $equipment): string;
    public function getDescription(Equipment $equipment): string;
}
