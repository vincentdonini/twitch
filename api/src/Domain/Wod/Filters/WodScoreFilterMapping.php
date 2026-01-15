<?php

namespace App\Domain\Wod\Filters;

final class WodScoreFilterMapping
{
    public const FIELD_MAP = [
        'user.id'        => 'user.id',
        'user.firstName' => 'user.firstName',
        'user.lastName'  => 'user.lastName',
        'wod.id'         => 'wod.id',
        'wod.name'       => 'wod.name',
        'division.id'    => 'wodVersion.wodDivision.id',
        'division.slug'  => 'wodVersion.wodDivision.slug',
        'gender'         => 'wodVariant.gender',
    ];
}
