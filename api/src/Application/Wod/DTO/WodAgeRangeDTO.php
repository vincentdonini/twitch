<?php

namespace App\Application\Wod\DTO;

use App\Application\DTO\BaseDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class WodAgeRangeDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_VERSION_LIST, FrontGroupsEnum::WOD_VERSION_DETAIL,
        FrontGroupsEnum::WOD_SCORE_LIST, FrontGroupsEnum::WOD_SCORE_DETAIL,
        FrontGroupsEnum::WOD_AGE_RANGE_LIST, FrontGroupsEnum::WOD_AGE_RANGE_DETAIL,
    ])]
    public int $id;

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
        int    $id,
        string $slug,
        string $title,
        ?int   $minAge,
        ?int   $maxAge,
        string $summary,
        string $details
    ) {
        parent::__construct($id);

        $this->slug    = $slug;
        $this->title   = $title;
        $this->minAge  = $minAge;
        $this->maxAge  = $maxAge;
        $this->summary = $summary;
        $this->details = $details;
    }
}
