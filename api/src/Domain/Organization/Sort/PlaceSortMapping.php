<?php

namespace App\Domain\Organization\Sort;

final class PlaceSortMapping
{
    public const FIELD_MAP = [
        'slug'            => 'slug',
        'name'            => 'name',
        'legalName'       => 'legalName',
        'city.slug'       => 'city.slug',
        'city.name'       => 'city.name',
        'department.code' => 'city.department.code',
        'department.slug' => 'city.department.slug',
        'department.name' => 'city.department.name',
        'region.code'     => 'city.department.region.code',
        'region.slug'     => 'city.department.region.slug',
        'region.name'     => 'city.department.region.name',
        'country.slug'    => 'city.department.region.country.slug',
        'country.name'    => 'city.department.region.country.name',
        'status'          => 'status',
    ];
}
