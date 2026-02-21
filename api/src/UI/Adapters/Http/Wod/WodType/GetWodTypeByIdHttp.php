<?php

namespace App\UI\Adapters\Http\Wod\WodType;

use App\Domain\Wod\WodType\GetWodTypeByIdDTOInterface;

final readonly class GetWodTypeByIdHttp implements GetWodTypeByIdDTOInterface
{
    public function __construct(
        private string $id,
    ) {

    }

    public function getId(): string
    {
        return $this->id;
    }
}
