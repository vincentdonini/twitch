<?php

namespace App\Domain\Equipment\Equipment;

use Symfony\Component\Uid\Uuid;

interface GetEquipmentByIdDTOInterface
{
    public function getId(): Uuid;
}
