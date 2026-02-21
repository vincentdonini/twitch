<?php

namespace App\UI\Adapters\Http\Wod\WodDivision;

use App\Domain\Wod\WodDivision\GetWodDivisionByIdDTOInterface;

final readonly class GetWodDivisionByIdHttp implements GetWodDivisionByIdDTOInterface
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
