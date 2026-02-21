<?php

namespace App\Domain\Achievement\Evaluator;

class CriteriaComparator
{
    public function compare(mixed $actual, string $operator, mixed $expected): bool
    {
        return match ($operator) {
            '>='      => $actual >= $expected,
            '<='      => $actual <= $expected,
            '=='      => $actual == $expected,
            'BETWEEN' => $actual >= $expected[0] && $actual <= $expected[1],
            'IN'      => in_array($actual, $expected, true),
            default   => false
        };
    }
}
