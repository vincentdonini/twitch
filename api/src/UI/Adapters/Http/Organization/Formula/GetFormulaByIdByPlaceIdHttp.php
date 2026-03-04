<?php

namespace App\UI\Adapters\Http\Organization\Formula;

use App\Domain\Organization\Formula\GetFormulaByIdByPlaceIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetFormulaByIdByPlaceIdHttp implements GetFormulaByIdByPlaceIdDTOInterface
{
    public function __construct(
        private Uuid $id,
        private Uuid $placeId,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPlaceId(): Uuid
    {
        return $this->placeId;
    }
}
