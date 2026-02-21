<?php

namespace App\Domain\Benchmark\Benchmark;

interface UpdateBenchmarkDTOInterface
{
    public function getId(): int;

    public function getExerciseId(): ?int;

    public function getName(): ?string;

    public function getType(): ?string;

    public function getValue(): ?string;

    public function getContents(): array;
}
