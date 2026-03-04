<?php

namespace App\UI\Adapters\Http\Benchmark\Benchmark;

use App\Domain\Benchmark\Benchmark\UpdateBenchmarkDTOInterface;
use App\Domain\Benchmark\Enum\TypeEnum;
use App\UI\Adapters\Http\Common\HttpPayloadParser;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateBenchmarkHttp implements UpdateBenchmarkDTOInterface
{
    use HttpPayloadParser;

    public function __construct(
        private Uuid  $id,
        private array $payload,
    ) {

    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getExerciseId(): ?Uuid
    {
        return $this->parseUuid('exerciseId');
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

    public function getValue(): ?string
    {
        return $this->payload['value'] ?? null;
    }

    public function getContents(): array
    {
        return $this->payload['contents'] ?? [];
    }
}
