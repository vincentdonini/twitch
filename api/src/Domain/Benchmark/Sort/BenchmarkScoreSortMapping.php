<?php

namespace App\Domain\Benchmark\Sort;

final class BenchmarkScoreSortMapping
{
    public const FIELD_MAP = [
        'performedAt'    => 'performedAt',
        'benchmark.name' => 'benchmark.name',
        'benchmark.slug' => 'benchmark.slug',
        'user.firstName' => 'user.firstName',
        'user.lastName'  => 'user.lastName',
    ];
}
