<?php

namespace App\Domain\Muscle\Service;

use App\Domain\Muscle\Entity\Muscle;

interface MuscleTranslatorInterface
{
    public function getName(Muscle $muscle): string;
    public function getDescription(Muscle $muscle): string;
}
