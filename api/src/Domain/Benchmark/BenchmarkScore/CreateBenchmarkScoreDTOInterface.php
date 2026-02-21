<?php

namespace App\Domain\Benchmark\BenchmarkScore;

interface CreateBenchmarkScoreDTOInterface
{
    public function getUserId(): int;

    public function getBenchmarkId(): int;

    public function getPerformedAt(): string;

    public function getTime(): ?int;

    public function getRepetitions(): ?int;

    public function getWeight(): ?int;

    public function getNote(): ?string;

    public function isPrivate(): bool;
}
