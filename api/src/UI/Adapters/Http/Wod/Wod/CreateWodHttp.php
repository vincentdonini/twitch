<?php

namespace App\UI\Adapters\Http\Wod\Wod;

use App\Domain\Wod\Wod\CreateWodDTOInterface;
use Symfony\Component\Uid\Uuid;

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

    public function getTypeId(): ?Uuid
    {
        return $this->payload['typeId'] ?? null;
    }

    public function getCategoryId(): ?Uuid
    {
        return $this->payload['categoryId'] ?? null;
    }

    public function getTeamSize(): ?int
    {
        return $this->payload['teamSize'] ?? null;
    }
}
