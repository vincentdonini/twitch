<?php

namespace App\UI\Adapters\Http\Benchmark\Benchmark;

use App\Domain\Benchmark\Benchmark\UpdateBenchmarkDTOInterface;

final readonly class UpdateBenchmarkHttp implements UpdateBenchmarkDTOInterface
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

    public function getExerciseId(): ?int
    {
        return $this->payload['exerciseId'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->payload['name'] ?? null;
    }

    public function getType(): ?string
    {
        return $this->payload['type'] ?? null;
    }

    public function getValue(): ?string
    {
        return $this->payload['value'] ?? null;
    }

    public function getContents(): array
    {
        return $this->payload['contents'] ?? [];
    }
}
