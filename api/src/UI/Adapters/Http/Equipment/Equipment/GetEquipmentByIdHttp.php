<?php

namespace App\UI\Adapters\Http\Equipment\Equipment;


use App\Domain\Equipment\Equipment\GetEquipmentByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetEquipmentByIdHttp implements GetEquipmentByIdDTOInterface
{
    public function __construct(
        private Uuid $id,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
