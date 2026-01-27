<?php

namespace App\Domain\Organization\Filters;

final class PlaceFilterMapping
{
    public const FIELD_MAP = [
        'company.id'        => 'company.id',
        'company.slug'      => 'company.slug',
        'company.name'      => 'company.name',
        'company.legalName' => 'company.legalName',
        'slug'              => 'slug',
        'name'              => 'name',
        'legalName'         => 'legalName',
        'siret'             => 'siret',
        'address'           => 'address',
        'postalCode'        => 'postalCode',
        'city.slug'         => 'city.slug',
        'city.name'         => 'city.name',
        'department.code'   => 'city.department.code',
        'department.slug'   => 'city.department.slug',
        'department.name'   => 'city.department.name',
        'region.code'       => 'city.department.region.code',
        'region.slug'       => 'city.department.region.slug',
        'region.name'       => 'city.department.region.name',
        'country.slug'      => 'city.department.region.country.slug',
        'country.name'      => 'city.department.region.country.name',
        'phone'             => 'phone',
        'email'             => 'email',
        'website'           => 'website',
        'status'            => 'status',
    ];
}
