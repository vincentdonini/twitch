<?php

namespace App\Application\Wod\DTO;

use App\Application\DTO\BaseDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class WodDivisionDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_DIVISION_LIST, FrontGroupsEnum::WOD_DIVISION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public int $id;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_DIVISION_LIST, FrontGroupsEnum::WOD_DIVISION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_DIVISION_LIST, FrontGroupsEnum::WOD_DIVISION_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_DIVISION_LIST, FrontGroupsEnum::WOD_DIVISION_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_DIVISION_DETAIL,
    ])]
    public string $details;

    public function __construct(
        int    $id,
        string $slug,
        string $title,
        string $summary,
        string $details
    ) {
        parent::__construct($id);

        $this->slug    = $slug;
        $this->title   = $title;
        $this->summary = $summary;
        $this->details = $details;
    }
}
