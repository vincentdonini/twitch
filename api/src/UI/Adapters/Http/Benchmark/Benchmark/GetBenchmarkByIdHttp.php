<?php

namespace App\UI\Adapters\Http\Benchmark\Benchmark;

use App\Domain\Benchmark\Benchmark\GetBenchmarkByIdDTOInterface;

final readonly class GetBenchmarkByIdHttp implements GetBenchmarkByIdDTOInterface
{
    public function __construct(
        private string $id,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
