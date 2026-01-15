<?php

namespace App\UI\Adapters\Http\Wod\Wod;

use App\Domain\Wod\Wod\UpdateWodDTOInterface;

final readonly class UpdateWodHttp implements UpdateWodDTOInterface
{
    public function __construct(
        private int   $id,
        private array $payload,
    ) {

    }

    public function getId(): int
    {
        return $this->id;
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
