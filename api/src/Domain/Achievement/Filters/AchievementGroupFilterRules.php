<?php

namespace App\Domain\Achievement\Filters;

use App\Domain\Common\Filter\Operator;

final class AchievementGroupFilterRules
{
    public const ALLOWED_OPERATORS = [
        'code'        => [Operator::EQ],
        'category.id' => [Operator::EQ],
    ];

    public const PUBLIC_FIELDS = [
        'code',
        'category.id',
    ];
}
