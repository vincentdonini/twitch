<?php

namespace App\Application\Achievement\Comparison;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class ScoreComparisonDTO
{
    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public ScoreDetailDTO $global;

    #[Groups([
        FrontGroupsEnum::ACHIEVEMENT_COMPARE,
    ])]
    public ScoreDetailDTO $duel;

    public function __construct(
        ScoreDetailDTO $global,
        ScoreDetailDTO $duel,
    ) {
        $this->global = $global;
        $this->duel   = $duel;
    }
}
