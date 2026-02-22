<?php

namespace App\UI\Adapters\Http\Wod\Wod;

use App\Domain\Wod\Wod\UpdateWodDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateWodHttp implements UpdateWodDTOInterface
{
    public function __construct(
        private Uuid  $id,
        private array $payload,
    ) {

    }

    public function getId(): Uuid
    {
        return $this->id;
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
