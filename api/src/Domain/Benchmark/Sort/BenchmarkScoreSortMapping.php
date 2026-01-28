<?php

namespace App\Domain\Benchmark\Sort;

final class BenchmarkScoreSortMapping
{
    public const FIELD_MAP = [
        'name'          => 'name',
        'exercise.id'   => 'exercise.id',
        'exercise.slug' => 'exercise.slug',
    ];
}
