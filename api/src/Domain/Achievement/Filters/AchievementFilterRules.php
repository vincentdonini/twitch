<?php

namespace App\Domain\Achievement\Filters;

use App\Domain\Common\Filter\Operator;

final class AchievementFilterRules
{
    public const ALLOWED_OPERATORS = [
        'code'              => [Operator::EQ],
        'group.id'          => [Operator::EQ],
        'group.category.id' => [Operator::EQ],
    ];

    public const PUBLIC_FIELDS = [
        'code',
        'group.id',
        'group.category.id',
    ];
}
