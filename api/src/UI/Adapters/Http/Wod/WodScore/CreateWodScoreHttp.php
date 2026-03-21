<?php

namespace App\UI\Adapters\Http\Wod\WodScore;

use App\Domain\Wod\WodScore\CreateWodScoreDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class CreateWodScoreHttp implements CreateWodScoreDTOInterface
{
    public function __construct(
        private array $payload,
        private Uuid  $userId,
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getWodId(): Uuid
    {
        return Uuid::fromString($this->payload['wodId']);
    }

    public function getWodVersionId(): Uuid
    {
        return Uuid::fromString($this->payload['wodVersionId']);
    }

    public function getPerformedAt(): string
    {
        return $this->payload['performedAt'];
    }

    public function getTime(): ?int
    {
        return $this->payload['time'] ?? null;
    }

    public function getRounds(): ?int
    {
        return $this->payload['rounds'] ?? null;
    }

    public function getRepetitions(): ?int
    {
        return $this->payload['repetitions'] ?? null;
    }

    public function getWeight(): ?int
    {
        return $this->payload['weight'] ?? null;
    }

    public function getNote(): ?string
    {
        return $this->payload['notes'] ?? null;
    }

    public function isPrivate(): bool
    {
        return $this->payload['private'];
    }
}
