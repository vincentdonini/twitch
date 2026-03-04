<?php

namespace App\UI\Adapters\Http\Benchmark\BenchmarkScore;

use App\Domain\Benchmark\BenchmarkScore\CreateBenchmarkScoreDTOInterface;
use App\UI\Adapters\Http\Common\HttpPayloadParser;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

final readonly class CreateBenchmarkScoreHttp implements CreateBenchmarkScoreDTOInterface
{
    use HttpPayloadParser;

    public function __construct(
        private array $payload,
        private Uuid  $userId,
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getBenchmarkId(): Uuid
    {
        return $this->parseUuid('benchmarkId') ?? throw new InvalidArgumentException('benchmarkId is required');
    }

    public function getPerformedAt(): DateTimeImmutable
    {
        return $this->parseDateTimeImmutable('performed_at', 'Y-m-d') ?? throw new InvalidArgumentException('performed_at is required');
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

    public function getNotes(): ?string
    {
        return $this->payload['notes'] ?? null;
    }

    public function isPrivate(): bool
    {
        return $this->parseBoolean('private') ?? false;
    }
}
