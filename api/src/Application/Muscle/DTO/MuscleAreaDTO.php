<?php

namespace App\Application\Muscle\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class MuscleAreaDTO
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

    public function __construct(
        int    $id,
        string $slug,
        string $name,
        string $description,
    ) {
        $this->id          = $id;
        $this->slug        = $slug;
        $this->name        = $name;
        $this->description = $description;
    }
}
