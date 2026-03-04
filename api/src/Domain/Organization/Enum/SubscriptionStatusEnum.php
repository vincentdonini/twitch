<?php

namespace App\Domain\Organization\Enum;

enum SubscriptionStatusEnum: string
{
    case PENDING   = 'PENDING';
    case ACTIVE    = 'ACTIVE';
    case CANCELLED = 'CANCELLED';
    case EXPIRED   = 'EXPIRED';
    case SUSPENDED = 'SUSPENDED';
}