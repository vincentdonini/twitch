<?php

namespace App\UI\Adapters\Http\Wod\WodScore;

use App\Domain\Wod\WodScore\CreateWodScoreDTOInterface;

final readonly class CreateWodScoreHttp implements CreateWodScoreDTOInterface
{
    public function __construct(
        private array $payload,
        private int   $userId,
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getWodId(): int
    {
        return $this->payload['wodId'];
    }

    public function getWodVersionId(): int
    {
        return $this->payload['wodVersionId'];
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
