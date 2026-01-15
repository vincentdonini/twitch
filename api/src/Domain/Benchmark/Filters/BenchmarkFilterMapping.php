<?php

namespace App\Domain\Benchmark\Filters;

final class BenchmarkFilterMapping
{
    public const FIELD_MAP = [
        'name'          => 'name',
        'exercise.id'   => 'exercise.id',
        'exercise.slug' => 'exercise.slug',
        'type'          => 'slug',
    ];
}
