<?php

namespace App\Domain\Geo\Enum;

enum TimezoneEnum: string
{
    case UTC = 'UTC';
    case EUROPE_PARIS = 'Europe/Paris';
}
