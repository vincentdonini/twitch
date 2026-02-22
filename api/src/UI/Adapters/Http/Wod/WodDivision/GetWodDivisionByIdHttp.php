<?php

namespace App\UI\Adapters\Http\Wod\WodDivision;

use App\Domain\Wod\WodDivision\GetWodDivisionByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetWodDivisionByIdHttp implements GetWodDivisionByIdDTOInterface
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
