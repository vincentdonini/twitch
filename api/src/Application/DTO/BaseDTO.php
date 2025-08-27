<?php

namespace App\Application\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

abstract class BaseDTO
{
    #[Groups(['default'])]
    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
