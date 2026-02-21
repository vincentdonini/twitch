<?php

namespace App\Domain\Achievement\Sort;

final class AchievementSortMapping
{
    public const FIELD_MAP = [
        'code'                         => 'code',
        'position'                     => 'position',
        'achievementCategory.id'       => 'achievementGroup.achievementCategory.id',
        'achievementCategory.position' => 'achievementGroup.achievementCategory.position',
        'achievementGroup.id'          => 'achievementGroup.id',
        'achievementGroup.position'    => 'achievementGroup.position',
    ];
}
