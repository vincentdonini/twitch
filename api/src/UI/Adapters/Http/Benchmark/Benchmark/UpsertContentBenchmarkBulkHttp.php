<?php

namespace App\UI\Adapters\Http\Benchmark\Benchmark;

use App\Domain\Benchmark\Benchmark\UpsertContentBenchmarkBulkDTOInterface;

final readonly class UpsertContentBenchmarkBulkHttp implements UpsertContentBenchmarkBulkDTOInterface
{
    public function __construct(
        private string $id,
        private array  $payload,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContents(): array
    {
        return $this->payload;
    }
}