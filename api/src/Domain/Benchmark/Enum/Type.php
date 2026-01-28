<?php

namespace App\Domain\Benchmark\Enum;

enum Type: string
{
    case WEIGHT = 'weight';
    case TIME = 'time';
    case REPETITIONS = 'repetitions';
}
