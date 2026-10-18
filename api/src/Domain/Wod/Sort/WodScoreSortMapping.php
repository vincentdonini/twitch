<?php

namespace App\Domain\Wod\Sort;

final class WodScoreSortMapping
{
    public const FIELD_MAP = [
        'performedAt'    => 'performedAt',
        'wod.name'       => 'wod.name',
        'user.firstName' => 'user.firstName',
        'user.lastName'  => 'user.lastName',
    ];
}
