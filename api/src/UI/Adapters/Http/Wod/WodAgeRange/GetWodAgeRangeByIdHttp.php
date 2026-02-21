<?php

namespace App\UI\Adapters\Http\Wod\WodAgeRange;

use App\Domain\Wod\WodAgeRange\GetWodAgeRangeByIdDTOInterface;

final readonly class GetWodAgeRangeByIdHttp implements GetWodAgeRangeByIdDTOInterface
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
