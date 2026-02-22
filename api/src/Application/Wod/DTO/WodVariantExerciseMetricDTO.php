<?php

namespace App\Application\Wod\DTO;

use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

class WodVariantExerciseMetricDTO
{
    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public Uuid $id;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public string $type;

    #[Groups([
        FrontGroupsEnum::WOD_LIST, FrontGroupsEnum::WOD_DETAIL,
    ])]
    public float|int $value;

    public function __construct(
        Uuid      $id,
        string    $type,
        float|int $value
    ) {
        $this->id    = $id;
        $this->type  = $type;
        $this->value = $value;
    }
}
