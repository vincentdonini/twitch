<?php

namespace App\Domain\Wod\Leaderboard;

use App\Domain\Wod\Entity\WodScore;

final readonly class RankedWodScore
{
    public function __construct(
        public int      $rank,
        public WodScore $score,
    ) {
    }
}
