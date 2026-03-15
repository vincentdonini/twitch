<?php

namespace App\Application\Wod\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class WodDivisionDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_DIVISION_LIST, FrontGroupsEnum::WOD_DIVISION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_LEADERBOARD,
    ])]
    public Uuid $id;

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

    #[Groups([
        FrontGroupsEnum::WOD_DIVISION_LIST, FrontGroupsEnum::WOD_DIVISION_DETAIL,
    ])]
    public int $wodCount;

    public function __construct(
        Uuid   $id,
        string $slug,
        string $title,
        string $summary,
        string $details,
        int    $wodCount = 0,
    ) {
        $this->id       = $id;
        $this->slug     = $slug;
        $this->title    = $title;
        $this->summary  = $summary;
        $this->details  = $details;
        $this->wodCount = $wodCount;
    }
}
