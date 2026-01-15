<?php

namespace App\UI\Adapters\Http\Wod\WodAgeRange;

use App\Domain\Wod\WodAgeRange\GetWodAgeRangeByIdDTOInterface;

class GetWodAgeRangeByIdHttp implements GetWodAgeRangeByIdDTOInterface
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
