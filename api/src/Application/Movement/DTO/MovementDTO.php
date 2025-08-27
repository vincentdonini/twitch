<?php

namespace App\Application\Movement\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class MovementDTO
{
    #[Groups(['movement:list', 'movement:detail'])]
    public int $id;

    #[Groups(['movement:list', 'movement:detail'])]
    public string $slug;

    #[Groups(['movement:list', 'movement:detail'])]
    public string $name;

    #[Groups(['movement:list', 'movement:detail'])]
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
