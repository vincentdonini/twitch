<?php

namespace App\Application\Equipment\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class EquipmentDTO
{
    #[Groups([
        FrontGroupsEnum::EQUIPMENT_LIST, FrontGroupsEnum::EQUIPMENT_DETAIL,
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::EQUIPMENT_LIST, FrontGroupsEnum::EQUIPMENT_DETAIL,
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::EQUIPMENT_LIST, FrontGroupsEnum::EQUIPMENT_DETAIL,
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public string $title;

    #[Groups([
        FrontGroupsEnum::EQUIPMENT_LIST, FrontGroupsEnum::EQUIPMENT_DETAIL,
    ])]
    public string $summary;

    #[Groups([
        FrontGroupsEnum::EQUIPMENT_LIST, FrontGroupsEnum::EQUIPMENT_DETAIL,
    ])]
    public string $details;

    #[Groups([
        FrontGroupsEnum::EQUIPMENT_LIST, FrontGroupsEnum::EQUIPMENT_DETAIL,
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
