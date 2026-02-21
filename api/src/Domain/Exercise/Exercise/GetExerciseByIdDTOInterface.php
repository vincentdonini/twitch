<?php

namespace App\Domain\Exercise\Exercise;

use Symfony\Component\Uid\Uuid;

interface GetExerciseByIdDTOInterface
{
    public function getId(): Uuid;
}
