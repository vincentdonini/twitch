<?php

namespace App\Domain\Muscle\MuscleArea;

use Symfony\Component\Uid\Uuid;

interface GetMuscleAreaByIdDTOInterface
{
    public function getId(): Uuid;
}
