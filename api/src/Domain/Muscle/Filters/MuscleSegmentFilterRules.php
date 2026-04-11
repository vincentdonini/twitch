<?php

namespace App\Domain\Muscle\Filters;

use App\Domain\Common\Filter\Operator;

final class MuscleSegmentFilterRules
{
    public const ALLOWED_OPERATORS = [
        'slug'             => [Operator::EQ],
        'muscle.id'        => [Operator::EQ],
        'contents.title'   => [Operator::EQ, Operator::LIKE],
        'contents.summary' => [Operator::LIKE],
        'contents.details' => [Operator::LIKE],
    ];

    public const PUBLIC_FIELDS = [
        'slug',
        'muscle.id',
        'contents.title',
        'contents.summary',
        'contents.details',
    ];
}
