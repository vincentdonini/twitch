<?php

namespace App\Domain\Exercise\Enum;

enum ExerciseAttributeTypeEnum: string
{
    case REPS = 'reps';
    case WEIGHT = 'weight';
    case CALORIES = 'calories';
    case DISTANCE = 'distance';
    case HEIGHT = 'height';
    case DURATION = 'duration';
}
