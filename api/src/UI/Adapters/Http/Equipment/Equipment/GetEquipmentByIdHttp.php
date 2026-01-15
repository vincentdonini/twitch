<?php

namespace App\UI\Adapters\Http\Equipment\Equipment;


use App\Domain\Equipment\Equipment\GetEquipmentByIdDTOInterface;

class GetEquipmentByIdHttp implements GetEquipmentByIdDTOInterface
{
    public function __construct(
        private readonly string $id,
    ) {

    }

    public function getId(): string
    {
        return $this->id;
    }
}
