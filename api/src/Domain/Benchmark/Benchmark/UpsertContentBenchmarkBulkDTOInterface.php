<?php

namespace App\Domain\Benchmark\Benchmark;

interface UpsertContentBenchmarkBulkDTOInterface
{
    public function getId(): string;
    public function getContents(): array;
}
