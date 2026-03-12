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
        'division.id'   => 'wodVariants.wodDivision.id',
        'division.slug' => 'wodVariants.wodDivision.slug',
        'gender'        => 'wodVariants.gender',
        'rounds'        => 'wodVariants.rounds',
        'timeCap'       => 'wodVariants.timeCap',
    ];
}
