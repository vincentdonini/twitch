<?php

namespace App\Domain\Muscle\Filters;

use App\Domain\Common\Filter\Operator;

final class MuscleAreaFilterRules
{
    public const ALLOWED_OPERATORS = [
        'slug'             => [
            Operator::EQ,
        ],
        'contents.title'   => [
            Operator::EQ,
            Operator::LIKE,
        ],
        'contents.summary' => [
            Operator::LIKE,
        ],
        'contents.details' => [
            Operator::LIKE,
        ],
    ];
}
