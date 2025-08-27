<?php

namespace App\Application\Exercise\DTO;

use App\Application\DTO\BaseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class ExerciseDTO extends BaseDTO
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

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public ?int $equipmentId;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public int $exerciseCategoryId;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public array $relationships = [];

    public function __construct(
        int    $id,
        string $slug,
        string $name,
        string $description,
        ?int   $equipmentId,
        int    $exerciseCategoryId,
    ) {
        parent::__construct($id);

        $this->slug               = $slug;
        $this->name               = $name;
        $this->description        = $description;
        $this->equipmentId        = $equipmentId;
        $this->exerciseCategoryId = $exerciseCategoryId;

        $this->relationships = [
            'equipment'        => ['type' => 'equipments', 'id' => $equipmentId],
            'exerciseCategory' => ['type' => 'exerciseCategories', 'id' => $exerciseCategoryId],
        ];
    }
}
