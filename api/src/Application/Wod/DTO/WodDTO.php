<?php

namespace App\Application\Wod\DTO;

use App\Application\DTO\BaseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class WodDTO extends BaseDTO
{
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public string $title;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public ?string $description;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public int $wodTypeId;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public int $wodCategoryId;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public array $relationships = [];

    public function __construct(
        int     $id,
        string  $title,
        ?string $description = null,
        int     $wodTypeId,
        int     $wodCategoryId,
    ) {
        parent::__construct($id);

        $this->title         = $title;
        $this->description   = $description;
        $this->wodTypeId     = $wodTypeId;
        $this->wodCategoryId = $wodCategoryId;

        $this->relationships = [
            'wodType'     => ['type' => 'wodTypes', 'id' => $wodTypeId],
            'wodCategory' => ['type' => 'wodCategories', 'id' => $wodCategoryId],
        ];
    }
}
