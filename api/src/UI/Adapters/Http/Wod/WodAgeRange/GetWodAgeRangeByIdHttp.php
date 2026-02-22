<?php

namespace App\UI\Adapters\Http\Wod\WodAgeRange;

use App\Domain\Wod\WodAgeRange\GetWodAgeRangeByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetWodAgeRangeByIdHttp implements GetWodAgeRangeByIdDTOInterface
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
