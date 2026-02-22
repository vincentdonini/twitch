<?php

namespace App\UI\Adapters\Http\Wod\WodType;

use App\Domain\Wod\WodType\GetWodTypeByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetWodTypeByIdHttp implements GetWodTypeByIdDTOInterface
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
