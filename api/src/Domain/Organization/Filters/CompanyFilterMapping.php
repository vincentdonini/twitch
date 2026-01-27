<?php

namespace App\Domain\Organization\Filters;

final class CompanyFilterMapping
{
    public const FIELD_MAP = [
        'slug'            => 'slug',
        'name'            => 'name',
        'legalName'       => 'legalName',
        'siren'           => 'siren',
        'vatNumber'       => 'vatNumber',
        'activityCode'    => 'activityCode',
        'legalForm'       => 'legalForm',
        'address'         => 'address',
        'postalCode'      => 'postalCode',
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
