<?php

namespace App\Domain\Organization\Enum;

enum FormulaBillingPeriodEnum: string
{
    case WEEKLY  = 'WEEKLY';
    case MONTHLY = 'MONTHLY';
    case YEARLY  = 'YEARLY';
}