<?php

namespace App\Application\Wod\DTO;

use App\Domain\Wod\Enum\GenderEnum;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class WodVariantDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public WodDivisionDTO $division;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public GenderEnum $gender;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
    ])]
    public ?WodAgeRangeDTO $ageRange;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public ?int $rounds;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public ?int $timeCap;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public array $exercises;

    public function __construct(
        Uuid            $id,
        WodDivisionDTO  $division,
        GenderEnum      $gender,
        ?WodAgeRangeDTO $ageRange,
        ?int            $rounds,
        ?int            $timeCap,
        array           $exercises
    ) {
        $this->id       = $id;
        $this->division = $division;
        $this->gender   = $gender;
        $this->ageRange = $ageRange;
        $this->rounds   = $rounds;
        $this->timeCap  = $timeCap;

        $this->exercises = $exercises;
    }
}
