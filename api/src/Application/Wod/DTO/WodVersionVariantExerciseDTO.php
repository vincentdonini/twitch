<?php

namespace App\Application\Wod\DTO;

use App\Application\DTO\BaseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class WodVersionVariantExerciseDTO extends BaseDTO
{
    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public int $position;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public int $reps;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public ?float $weight;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public int $exerciseId;

    #[Groups([
        'wod:list', 'wod:detail',
    ])]
    public array $relationships = [];

    public function __construct(
        int    $id,
        int    $position,
        int    $reps,
        ?float $weight,
        int    $wodVersionVariantId,
        int    $exerciseId,
    ) {
        parent::__construct($id);

        $this->position   = $position;
        $this->reps       = $reps;
        $this->weight     = $weight;
        $this->exerciseId = $exerciseId;

        $this->relationships = [
            'wodVersionVariant' => ['type' => 'wodVersionVariants', 'id' => $wodVersionVariantId],
            'exercise'          => ['type' => 'exercises', 'id' => $exerciseId],
        ];
    }
}
