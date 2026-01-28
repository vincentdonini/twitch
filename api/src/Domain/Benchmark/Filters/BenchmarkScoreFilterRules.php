<?php

namespace App\Domain\Benchmark\Filters;

use App\Domain\Common\Filter\Operator;

final class BenchmarkScoreFilterRules
{
    public const ALLOWED_OPERATORS = [
        'user.id'        => [Operator::EQ],
        'user.firstName' => [Operator::EQ, Operator::LIKE],
        'user.lastName'  => [Operator::EQ, Operator::LIKE],
        'benchmark.id'   => [Operator::EQ],
        'benchmark.slug' => [Operator::EQ],
        'benchmark.name' => [Operator::EQ, Operator::LIKE],
    ];

    public const PUBLIC_FIELDS = [
        'user.id',
        'user.firstName',
        'user.lastName',
        'benchmark.id',
        'benchmark.slug',
        'benchmark.name',
    ];
}
