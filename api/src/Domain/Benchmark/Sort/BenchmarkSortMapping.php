<?php

namespace App\Domain\Benchmark\Sort;

final class BenchmarkSortMapping
{
    public const FIELD_MAP = [
        'name'          => 'name',
        'exercise.id'   => 'exercise.id',
        'exercise.slug' => 'exercise.slug',
    ];
}
