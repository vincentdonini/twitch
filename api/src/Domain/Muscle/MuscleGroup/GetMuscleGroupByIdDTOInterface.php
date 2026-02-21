<?php

namespace App\Domain\Muscle\MuscleGroup;

use Symfony\Component\Uid\Uuid;

interface GetMuscleGroupByIdDTOInterface
{
    public function getId(): Uuid;
}
