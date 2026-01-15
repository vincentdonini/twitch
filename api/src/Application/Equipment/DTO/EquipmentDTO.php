<?php

namespace App\Application\Equipment\DTO;

use App\Application\DTO\BaseDTO;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class EquipmentDTO extends BaseDTO
{
    #[Groups([
        FrontGroupsEnum::EQUIPMENT_LIST, FrontGroupsEnum::EQUIPMENT_DETAIL,
    ])]
    public string $slug;

    #[Groups([
        FrontGroupsEnum::EQUIPMENT_LIST, FrontGroupsEnum::EQUIPMENT_DETAIL,
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

