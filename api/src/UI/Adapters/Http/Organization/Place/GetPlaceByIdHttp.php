<?php

namespace App\UI\Adapters\Http\Organization\Place;

use App\Domain\Organization\Place\GetPlaceByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetPlaceByIdHttp implements GetPlaceByIdDTOInterface
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
