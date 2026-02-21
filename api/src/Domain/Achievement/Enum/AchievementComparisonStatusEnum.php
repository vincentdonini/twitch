<?php

namespace App\Domain\Achievement\Enum;

enum AchievementComparisonStatusEnum: string
{
    case COMMON     = 'COMMON';
    case USER_ONLY  = 'USER_ONLY';
    case OTHER_ONLY = 'OTHER_ONLY';
    case NONE       = 'NONE';

    public static function fromProgress(int $userProgress, int $otherProgress): self
    {
        return match (true) {
            $userProgress > 0 && $otherProgress > 0 => self::COMMON,
            $userProgress > 0                       => self::USER_ONLY,
            $otherProgress > 0                      => self::OTHER_ONLY,
            default                                 => self::NONE,
        };
    }

    public function isCommon(): bool
    {
        return $this === self::COMMON;
    }

    public function isUserOnly(): bool
    {
        return $this === self::USER_ONLY;
    }

    public function isOtherOnly(): bool
    {
        return $this === self::OTHER_ONLY;
    }
}
