<?php

namespace App\Domain\Achievement\Enum;

enum AchievementRarityEnum: string
{
    case COMMON     = 'COMMON';
    case UNCOMMON   = 'UNCOMMON';
    case RARE       = 'RARE';
    case VERY_RARE  = 'VERY_RARE';
    case ULTRA_RARE = 'ULTRA_RARE';

    public static function fromRatio(float $ratio): self
    {
        $ratio = max(0, min(1, $ratio));

        return match (true) {
            $ratio >= 0.50 => self::COMMON,
            $ratio >= 0.25 => self::UNCOMMON,
            $ratio >= 0.10 => self::RARE,
            $ratio >= 0.05 => self::VERY_RARE,
            default        => self::ULTRA_RARE,
        };
    }
}