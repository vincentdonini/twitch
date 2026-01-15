<?php

namespace App\UI\Adapters\Http\Benchmark\BenchmarkScore;

use App\Domain\Benchmark\BenchmarkScore\CreateBenchmarkScoreDTOInterface;

final readonly class CreateBenchmarkScoreHttp implements CreateBenchmarkScoreDTOInterface
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

    public function getBenchmarkId(): int
    {
        return $this->payload['benchmarkId'];
    }

    public function getPerformedAt(): string
    {
        return $this->payload['performedAt'];
    }

    public function getTime(): ?int
    {
        return $this->payload['time'] ?? null;
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
