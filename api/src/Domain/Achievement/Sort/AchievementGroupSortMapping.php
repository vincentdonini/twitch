<?php

namespace App\Domain\Achievement\Sort;

final class AchievementGroupSortMapping
{
    public const FIELD_MAP = [
        'code'                   => 'code',
        'position'               => 'position',
        'achievementCategory.id' => 'achievementCategory.id',
    ];
}
