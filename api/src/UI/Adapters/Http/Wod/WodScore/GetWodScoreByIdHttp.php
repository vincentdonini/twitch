<?php

namespace App\UI\Adapters\Http\Wod\WodScore;

use App\Domain\Wod\WodScore\GetWodScoreByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetWodScoreByIdHttp implements GetWodScoreByIdDTOInterface
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
