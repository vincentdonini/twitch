<?php

namespace App\UI\Adapters\Http\Wod\WodScore;

use App\Domain\Wod\WodScore\GetWodScoreByIdDTOInterface;

final readonly class GetWodScoreByIdHttp implements GetWodScoreByIdDTOInterface
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
