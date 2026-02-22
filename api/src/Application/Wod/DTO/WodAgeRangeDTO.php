<?php

namespace App\Application\Wod\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class WodAgeRangeDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_AGE_RANGE_LIST, FrontGroupsEnum::WOD_AGE_RANGE_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_AGE_RANGE_LIST, FrontGroupsEnum::WOD_AGE_RANGE_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_AGE_RANGE_LIST, FrontGroupsEnum::WOD_AGE_RANGE_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_AGE_RANGE_LIST, FrontGroupsEnum::WOD_AGE_RANGE_DETAIL,
    ])]
    public ?int $minAge;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_AGE_RANGE_LIST, FrontGroupsEnum::WOD_AGE_RANGE_DETAIL,
    ])]
    public ?int $maxAge;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_AGE_RANGE_LIST, FrontGroupsEnum::WOD_AGE_RANGE_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_AGE_RANGE_DETAIL,
    ])]
    public string $details;

    public function __construct(
        Uuid   $id,
        string $slug,
        string $title,
        ?int   $minAge,
        ?int   $maxAge,
        string $summary,
        string $details
    ) {
        $this->id      = $id;
        $this->slug    = $slug;
        $this->title   = $title;
        $this->minAge  = $minAge;
        $this->maxAge  = $maxAge;
        $this->summary = $summary;
        $this->details = $details;
    }
}
