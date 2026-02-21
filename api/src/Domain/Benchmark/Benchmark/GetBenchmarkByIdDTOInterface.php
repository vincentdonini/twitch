<?php

namespace App\Domain\Benchmark\Benchmark;

use Symfony\Component\Uid\Uuid;

interface GetBenchmarkByIdDTOInterface
{
    public function getId(): Uuid;
}
