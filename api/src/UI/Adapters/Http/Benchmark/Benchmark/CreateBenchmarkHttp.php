<?php

namespace App\UI\Adapters\Http\Benchmark\Benchmark;

use App\Domain\Benchmark\Benchmark\CreateBenchmarkDTOInterface;

final readonly class CreateBenchmarkHttp implements CreateBenchmarkDTOInterface
{
    public function __construct(
        private array $payload,
    ) {

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

    public function getValue(): ?int
    {
        return $this->payload['value'] ?? null;
    }
}
