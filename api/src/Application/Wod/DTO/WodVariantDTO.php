<?php

namespace App\Application\Wod\DTO;

use App\Application\DTO\BaseDTO;
use App\Domain\Wod\Enum\Gender;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class WodVariantDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public int $id;

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
    public Gender $gender;

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
        int             $id,
        WodDivisionDTO  $division,
        Gender          $gender,
        ?WodAgeRangeDTO $ageRange,
        ?int            $rounds,
        ?int            $timeCap,
        array           $exercises
    ) {
        parent::__construct($id);

        $this->division = $division;
        $this->gender   = $gender;
        $this->ageRange = $ageRange;
        $this->rounds   = $rounds;
        $this->timeCap  = $timeCap;

        $this->exercises = $exercises;
    }
}
