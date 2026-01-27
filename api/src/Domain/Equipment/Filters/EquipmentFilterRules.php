<?php

namespace App\Domain\Equipment\Filters;

use App\Domain\Common\Filter\Operator;

final class EquipmentFilterRules
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
