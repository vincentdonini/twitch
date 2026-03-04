<?php

namespace App\Domain\Organization\Enum;

enum FormulaTypeEnum: string
{
    case SUBSCRIPTION = 'SUBSCRIPTION';
    case PACK         = 'PACK';
    case DROP_IN      = 'DROP_IN';
}
