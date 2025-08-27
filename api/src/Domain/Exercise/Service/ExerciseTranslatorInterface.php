<?php

namespace App\Domain\Exercise\Service;

use App\Domain\Exercise\Entity\Exercise;

interface ExerciseTranslatorInterface
{
    public function getName(Exercise $exercise): string;
    public function getDescription(Exercise $exercise): string;
}
