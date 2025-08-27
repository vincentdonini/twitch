<?php

namespace App\Application\Wod\DTO;

use App\Application\DTO\BaseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class WodVersionDTO extends BaseDTO
{
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public int $wodId;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public int $wodVersionTypeId;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public array $relationships = [];

    public function __construct(
        int $id,
        int $wodId,
        int $wodVersionTypeId
    ) {
        parent::__construct($id);

        $this->wodId            = $wodId;
        $this->wodVersionTypeId = $wodVersionTypeId;

        $this->relationships = [
            'wod'            => ['type' => 'wods', 'id' => $wodId],
            'wodVersionType' => ['type' => 'wodVersionTypes', 'id' => $wodVersionTypeId],
        ];
    }
}
