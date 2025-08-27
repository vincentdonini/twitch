<?php

namespace App\Application\Wod\DTO;

use App\Application\DTO\BaseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class WodVersionTypeDTO extends BaseDTO
{
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public string $slug;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public string $name;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public string $description;

    public function __construct(
        int    $id,
        string $slug,
        string $name,
        string $description,
    ) {
        parent::__construct($id);

        $this->slug        = $slug;
        $this->name        = $name;
        $this->description = $description;
    }
}
