<?php

namespace App\Domain\Organization\Enum;

enum FormulaStatusEnum: string
{
    case ACTIVE   = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case ARCHIVED = 'ARCHIVED';
}
