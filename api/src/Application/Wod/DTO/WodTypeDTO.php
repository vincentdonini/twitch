<?php

namespace App\Application\Wod\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class WodTypeDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_TYPE_LIST, FrontGroupsEnum::WOD_TYPE_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_TYPE_LIST, FrontGroupsEnum::WOD_TYPE_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_TYPE_LIST, FrontGroupsEnum::WOD_TYPE_DETAIL,
    ])]
    public array $allowedMetrics;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_TYPE_LIST, FrontGroupsEnum::WOD_TYPE_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_TYPE_LIST, FrontGroupsEnum::WOD_TYPE_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::WOD_DETAIL,
        FrontGroupsEnum::WOD_TYPE_DETAIL,
    ])]
    public string $details;

    #[Groups([
        FrontGroupsEnum::WOD_TYPE_LIST, FrontGroupsEnum::WOD_TYPE_DETAIL,
    ])]
    public int $wodCount;

    public function __construct(
        Uuid   $id,
        string $slug,
        array  $allowedMetrics,
        string $title,
        string $summary,
        string $details,
        int    $wodCount = 0,
    ) {
        $this->id             = $id;
        $this->slug           = $slug;
        $this->allowedMetrics = $allowedMetrics;
        $this->title          = $title;
        $this->summary        = $summary;
        $this->details        = $details;
        $this->wodCount       = $wodCount;
    }
}
