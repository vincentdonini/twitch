<?php

namespace App\Domain\Achievement\Enum;

enum AchievementLevelEnum: string
{
    case DEFAULT  = 'DEFAULT';
    case BRONZE   = 'BRONZE';
    case SILVER   = 'SILVER';
    case GOLD     = 'GOLD';
    case PLATINUM = 'PLATINUM';

    public function order(): int
    {
        return match ($this) {
            self::DEFAULT  => 0,
            self::BRONZE   => 1,
            self::SILVER   => 2,
            self::GOLD     => 3,
            self::PLATINUM => 999,
        };
    }
}
