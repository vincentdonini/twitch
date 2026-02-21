<?php

namespace App\Application\Achievement\Comparison;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class ScoreDetailDTO
{
    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public int $user;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public int $other;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public string $winner;

    public function __construct(
        int    $user,
        int    $other,
        string $winner,
    ) {
        $this->user   = $user;
        $this->other  = $other;
        $this->winner = $winner;
    }
}
