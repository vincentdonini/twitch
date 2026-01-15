<?php

namespace App\Domain\Benchmark\Benchmark;

interface CreateBenchmarkDTOInterface
{
    public function getExerciseId(): ?int;
    public function getName(): ?string;
    public function getType(): ?string;
}
