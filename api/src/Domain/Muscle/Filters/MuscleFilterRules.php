<?php

namespace App\Domain\Muscle\Filters;

use App\Domain\Common\Filter\Operator;

final class MuscleFilterRules
{
    public const ALLOWED_OPERATORS = [
        'slug'             => [Operator::EQ],
        'contents.title'   => [Operator::EQ, Operator::LIKE],
        'contents.summary' => [Operator::LIKE],
        'contents.details' => [Operator::LIKE],
    ];

    public const PUBLIC_FIELDS = [
        'slug',
        'contents.title',
        'contents.summary',
        'contents.details',
    ];
}
