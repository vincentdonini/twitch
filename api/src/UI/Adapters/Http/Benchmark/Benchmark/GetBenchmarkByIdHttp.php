<?php

namespace App\UI\Adapters\Http\Benchmark\Benchmark;

use App\Domain\Benchmark\Benchmark\GetBenchmarkByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetBenchmarkByIdHttp implements GetBenchmarkByIdDTOInterface
{
    public function __construct(
        private Uuid $id,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
