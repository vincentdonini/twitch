<?php

namespace App\Domain\Benchmark\Filters;

final class BenchmarkScoreFilterMapping
{
    public const FIELD_MAP = [
        'user.id'        => 'user.id',
        'user.firstName' => 'user.firstName',
        'user.lastName'  => 'user.lastName',
        'benchmark.id'   => 'benchmark.id',
        'benchmark.slug' => 'benchmark.slug',
        'benchmark.name' => 'benchmark.name',
    ];
}
