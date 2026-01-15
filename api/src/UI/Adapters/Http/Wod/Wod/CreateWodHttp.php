<?php

namespace App\UI\Adapters\Http\Wod\Wod;

use App\Domain\Wod\Wod\CreateWodDTOInterface;

final readonly class CreateWodHttp implements CreateWodDTOInterface
{
    public function __construct(
        private array $payload,
    ) {

    }

    public function getName(): ?string
    {
        return $this->payload['name'] ?? null;
    }

    public function getTypeId(): ?int
    {
        return $this->payload['typeId'] ?? null;
    }

    public function getCategoryId(): ?int
    {
        return $this->payload['categoryId'] ?? null;
    }

    public function getTeamSize(): ?int
    {
        return $this->payload['teamSize'] ?? null;
    }
}
