<?php

namespace App\Domain\Benchmark\Benchmark;

interface UpsertContentBenchmarkDTOInterface
{
    public function getId(): string;

    public function getLocale(): string;

    public function getTitle(): ?string;

    public function getSummary(): ?string;

    public function getDetails(): ?string;
}
