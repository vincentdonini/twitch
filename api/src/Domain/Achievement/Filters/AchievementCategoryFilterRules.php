<?php

namespace App\Domain\Achievement\Filters;

use App\Domain\Common\Filter\Operator;

final class AchievementCategoryFilterRules
{
    public const ALLOWED_OPERATORS = [
        'code' => [Operator::EQ],
    ];

    public const PUBLIC_FIELDS = [
        'code',
    ];
}
