<?php

namespace App\Domain\Equipment\Equipment;

use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Equipment\Ports\EquipmentDALInterface;

final readonly class GetEquipmentByIdUseCase
{
    public function __construct(
        private EquipmentDALInterface $equipmentDAL,
    ) {
    }

    public function execute(GetEquipmentByIdDTOInterface $dto): Equipment
    {
        $equipment = $this->equipmentDAL->getById($dto->getId());
        if(!$equipment instanceof Equipment){
            throw new EntityNotFoundException();
        }

        return $equipment;
    }
}

