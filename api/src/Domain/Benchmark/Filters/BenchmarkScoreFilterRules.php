<?php

namespace App\Domain\Benchmark\Filters;

use App\Domain\Common\Filter\Operator;

final class BenchmarkScoreFilterRules
{
    public const ALLOWED_OPERATORS = [
        'user.id'        => [
            Operator::EQ,
        ],
        'user.firstName' => [
            Operator::EQ,
            Operator::LIKE,
        ],
        'user.lastName'  => [
            Operator::EQ,
            Operator::LIKE,
        ],
        'benchmark.id'   => [
            Operator::EQ,
        ],
        'benchmark.name' => [
            Operator::EQ,
            Operator::LIKE,
        ],
    ];
}
