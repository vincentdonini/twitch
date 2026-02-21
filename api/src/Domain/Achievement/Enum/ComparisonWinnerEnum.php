<?php

namespace App\Domain\Achievement\Enum;

enum ComparisonWinnerEnum: string
{
    case USER  = 'USER';
    case OTHER = 'OTHER';
    case DRAW  = 'DRAW';

    public static function fromScores(int $userScore, int $otherScore): self
    {
        return match (true) {
            $userScore > $otherScore => self::USER,
            $otherScore > $userScore => self::OTHER,
            default                  => self::DRAW,
        };
    }

    public function isUser(): bool
    {
        return $this === self::USER;
    }

    public function isOther(): bool
    {
        return $this === self::OTHER;
    }

    public function isDraw(): bool
    {
        return $this === self::DRAW;
    }
}
