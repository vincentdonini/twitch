<?php

namespace App\Domain\Benchmark\Benchmark;

use App\Domain\Benchmark\Enum\TypeEnum;
use Symfony\Component\Uid\Uuid;

interface CreateBenchmarkDTOInterface
{
    public function getExerciseId(): ?Uuid;

    public function getName(): ?string;

    public function getType(): ?TypeEnum;
}
