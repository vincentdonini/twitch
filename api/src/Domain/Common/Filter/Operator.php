<?php

namespace App\Domain\Common\Filter;

enum Operator: string
{
    case EQ  = 'eq';
    case NEQ = 'neq';

    case LT  = 'lt';
    case LTE = 'lte';
    case GT  = 'gt';
    case GTE = 'gte';

    case LIKE     = 'like';
    case NOT_LIKE = 'notLike';

    case IN     = 'in';
    case NOT_IN = 'notIn';

    case BETWEEN = 'between';
}
