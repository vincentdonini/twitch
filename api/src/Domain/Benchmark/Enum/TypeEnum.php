<?php

namespace App\Domain\Benchmark\Enum;

enum TypeEnum: string
{
    case WEIGHT = 'weight';
    case TIME = 'time';
    case REPETITIONS = 'repetitions';
}
