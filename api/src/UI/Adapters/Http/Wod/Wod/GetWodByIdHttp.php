<?php

namespace App\UI\Adapters\Http\Wod\Wod;

use App\Domain\Wod\Wod\GetWodByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetWodByIdHttp implements GetWodByIdDTOInterface
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
