<?php

namespace App\Application\Muscle\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class MuscleDTO
{
    #[Groups([
        'muscle:list', 'muscle:detail',
        'muscleArea:list', 'muscleArea:detail',
        'muscleGroup:list', 'muscleGroup:detail',
    ])]
    public int $id;

    #[Groups([
        'muscle:list', 'muscle:detail',
        'muscleArea:list', 'muscleArea:detail',
        'muscleGroup:list', 'muscleGroup:detail',
    ])]
    public string $slug;

    #[Groups([
        'muscle:list', 'muscle:detail',
        'muscleArea:list', 'muscleArea:detail',
        'muscleGroup:list', 'muscleGroup:detail',
    ])]
    public string $name;

    #[Groups([
        'muscle:list', 'muscle:detail',
        'muscleArea:list', 'muscleArea:detail',
        'muscleGroup:list', 'muscleGroup:detail',
    ])]
    public string $description;

    #[Groups([
        'muscle:list', 'muscle:detail',
        'muscleArea:list', 'muscleArea:detail',
        'muscleGroup:list', 'muscleGroup:detail',
    ])]
    public MuscleAreaDTO $area;

    #[Groups([
        'muscle:list', 'muscle:detail',
        'muscleArea:list', 'muscleArea:detail',
        'muscleGroup:list', 'muscleGroup:detail',
    ])]
    public ?MuscleGroupDTO $group = null;

    public function __construct(
        int             $id,
        string          $slug,
        string          $name,
        string          $description,
        MuscleAreaDTO   $area,
        ?MuscleGroupDTO $group,
    ) {
        $this->id          = $id;
        $this->slug        = $slug;
        $this->name        = $name;
        $this->description = $description;
        $this->area        = $area;
        $this->group       = $group;
    }
}
