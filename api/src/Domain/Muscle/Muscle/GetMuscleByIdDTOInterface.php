<?php

namespace App\Domain\Muscle\Muscle;

use Symfony\Component\Uid\Uuid;

interface GetMuscleByIdDTOInterface
{
    public function getId(): Uuid;
}
