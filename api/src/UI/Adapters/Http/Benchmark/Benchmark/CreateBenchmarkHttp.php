<?php

namespace App\UI\Adapters\Http\Benchmark\Benchmark;

use App\Domain\Benchmark\Benchmark\CreateBenchmarkDTOInterface;
use App\Domain\Benchmark\Enum\TypeEnum;
use App\UI\Adapters\Http\Common\HttpPayloadParser;
use Symfony\Component\Uid\Uuid;

final readonly class CreateBenchmarkHttp implements CreateBenchmarkDTOInterface
{
    use HttpPayloadParser;

    public function __construct(
        private array $payload,
    ) {
    }

    public function getExerciseId(): ?Uuid
    {
        return $this->parseUuid('exerciseId') ?? null;
    }

    public function getName(): ?string
    {
        return $this->payload['name'] ?? null;
    }

    public function getType(): ?TypeEnum
    {
        /** @var TypeEnum|null */
        return $this->parseEnum('type', TypeEnum::class);
    }

    public function getValue(): ?int
    {
        return $this->payload['value'] ?? null;
    }
}
