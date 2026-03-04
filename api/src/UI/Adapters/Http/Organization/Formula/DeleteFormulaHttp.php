<?php

namespace App\UI\Adapters\Http\Organization\Formula;

use App\Domain\Organization\Formula\DeleteFormulaDTOInterface;
use App\UI\Adapters\Http\Common\HttpPayloadParser;
use Symfony\Component\Uid\Uuid;

final readonly class DeleteFormulaHttp implements DeleteFormulaDTOInterface
{
    use HttpPayloadParser;

    public function __construct(
        private Uuid  $id,
        private Uuid  $placeId,
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
