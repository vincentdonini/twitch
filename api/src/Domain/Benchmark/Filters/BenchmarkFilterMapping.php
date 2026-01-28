<?php

namespace App\Domain\Benchmark\Filters;

final class BenchmarkFilterMapping
{
    public const FIELD_MAP = [
        'slug'          => 'slug',
        'name'          => 'name',
        'type'          => 'type',
        'exercise.id'   => 'exercise.id',
        'exercise.slug' => 'exercise.slug',
    ];
}
