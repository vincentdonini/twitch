<?php

namespace App\Domain\Wod\Enum;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case MIXED = 'mixed';
}
