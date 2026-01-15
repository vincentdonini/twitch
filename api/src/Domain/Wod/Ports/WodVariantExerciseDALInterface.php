<?php

namespace App\Domain\Wod\Ports;

use App\Domain\Wod\Entity\WodVariantExercise;

interface WodVariantExerciseDALInterface
{
    public function getById(string $id): ?WodVariantExercise;
}
