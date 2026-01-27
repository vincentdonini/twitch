<?php

namespace App\Domain\Benchmark\Filters;

use App\Domain\Common\Filter\Operator;

final class BenchmarkFilterRules
{
    public const ALLOWED_OPERATORS = [
        'name'          => [Operator::EQ, Operator::LIKE],
        'exercise.id'   => [Operator::EQ],
        'exercise.slug' => [Operator::EQ, Operator::LIKE],
        'type'          => [Operator::EQ, Operator::LIKE],
    ];

    public const PUBLIC_FIELDS = [
        'name',
        'exercise.id',
        'exercise.slug',
        'type',
    ];
}
