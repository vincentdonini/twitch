<?php

namespace App\Domain\Benchmark\BenchmarkScore;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

interface CreateBenchmarkScoreDTOInterface
{
    public function getUserId(): Uuid;

    public function getBenchmarkId(): Uuid;

    public function getPerformedAt(): DateTimeImmutable;

    public function getTime(): ?int;

    public function getRepetitions(): ?int;

    public function getWeight(): ?int;

    public function getNotes(): ?string;

    public function isPrivate(): bool;
}
