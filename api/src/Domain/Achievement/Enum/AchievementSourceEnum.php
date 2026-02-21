<?php

namespace App\Domain\Achievement\Enum;

enum AchievementSourceEnum: string
{
    case WOD = 'wod';
    case BENCHMARK = 'benchmark';
    case SOCIAL = 'social';
}
