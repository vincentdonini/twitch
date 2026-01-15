<?php

namespace App\Domain\Wod\Sort;

final class WodSortMapping
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
        'gender'        => 'wodVersions.wodVariants.gender',
        'timeCap'       => 'wodVersions.wodVariants.timeCap',
    ];
}
