<?php

namespace App\Domain\Benchmark\Filters;

use App\Domain\Common\Filter\Operator;

final class BenchmarkFilterRules
{
    public const ALLOWED_OPERATORS = [
        'slug'          => [Operator::EQ],
        'name'          => [Operator::EQ, Operator::LIKE],
        'category'      => [Operator::EQ],
        'type'          => [Operator::EQ],
        'exercise.id'   => [Operator::EQ],
        'exercise.slug' => [Operator::EQ, Operator::LIKE],
    ];

    public const PUBLIC_FIELDS = [
        'slug',
        'name',
        'category',
        'type',
        'exercise.id',
        'exercise.slug',
    ];
}
