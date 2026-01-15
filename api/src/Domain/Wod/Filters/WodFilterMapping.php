<?php

namespace App\Domain\Wod\Filters;

final class WodFilterMapping
{
    public const FIELD_MAP = [
        'name'          => 'name',
        'teamSize'      => 'teamSize',
        'type.id'       => 'wodType.id',
        'type.slug'     => 'wodType.slug',
        'category.id'   => 'wodCategory.id',
        'category.slug' => 'wodCategory.slug',
        'division.id'   => 'wodVersions.wodDivision.id',
        'division.slug' => 'wodVersions.wodDivision.slug',
        'gender'        => 'wodVersions.gender',
        'rounds'        => 'wodVersions.wodVariants.rounds',
        'timeCap'       => 'wodVersions.wodVariants.timeCap',
    ];
}
