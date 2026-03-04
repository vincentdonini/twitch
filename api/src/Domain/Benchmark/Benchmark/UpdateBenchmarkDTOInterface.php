<?php

namespace App\Domain\Benchmark\Benchmark;

use App\Domain\Benchmark\Enum\TypeEnum;
use Symfony\Component\Uid\Uuid;

interface UpdateBenchmarkDTOInterface
{
    public function getId(): Uuid;

    public function getExerciseId(): ?Uuid;

    public function getName(): ?string;

    public function getType(): ?TypeEnum;

    public function getValue(): ?string;

    public function getContents(): array;
}
