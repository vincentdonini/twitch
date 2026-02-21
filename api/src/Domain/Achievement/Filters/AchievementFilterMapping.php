<?php

namespace App\Domain\Achievement\Filters;

final class AchievementFilterMapping
{
    public const FIELD_MAP = [
        'code'        => 'code',
        'group.id'    => 'group.id',
        'category.id' => 'group.category.id',
    ];
}
