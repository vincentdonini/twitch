<?php

namespace App\Application\Muscle\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class MuscleGroupDTO
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

    public function __construct(
        int           $id,
        string        $slug,
        string        $name,
        string        $description,
        MuscleAreaDTO $area,
    ) {
        $this->id          = $id;
        $this->slug        = $slug;
        $this->name        = $name;
        $this->description = $description;
        $this->area        = $area;
    }
}
