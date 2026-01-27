<?php

namespace App\Domain\Organization\Filters;

use App\Domain\Common\Filter\Operator;

final class PlaceFilterRules
{
    public const ALLOWED_OPERATORS = [
        'company.id'                          => [Operator::EQ],
        'company.slug'                        => [Operator::EQ],
        'company.name'                        => [Operator::EQ, Operator::LIKE],
        'company.legalName'                   => [Operator::EQ, Operator::LIKE],
        'slug'                                => [Operator::EQ],
        'name'                                => [Operator::EQ, Operator::LIKE],
        'legalName'                           => [Operator::EQ, Operator::LIKE],
        'siret'                               => [Operator::EQ],
        'address'                             => [Operator::LIKE],
        'postalCode'                          => [Operator::EQ],
        'city.slug'                           => [Operator::EQ],
        'city.name'                           => [Operator::EQ, Operator::LIKE],
        'city.department.code'                => [Operator::EQ],
        'city.department.slug'                => [Operator::EQ],
        'city.department.name'                => [Operator::EQ, Operator::LIKE],
        'city.department.region.code'         => [Operator::EQ],
        'city.department.region.slug'         => [Operator::EQ],
        'city.department.region.name'         => [Operator::EQ, Operator::LIKE],
        'city.department.region.country.slug' => [Operator::EQ],
        'city.department.region.country.name' => [Operator::EQ, Operator::LIKE],
        'phone'                               => [Operator::EQ],
        'email'                               => [Operator::EQ],
        'website'                             => [Operator::LIKE],
        'status'                              => [Operator::EQ],
    ];

    public const PUBLIC_FIELDS = [
        'slug',
        'name',
        'address',
        'postalCode',
        'city.slug',
        'city.name',
        'city.department.code',
        'city.department.slug',
        'city.department.name',
        'city.department.region.code',
        'city.department.region.slug',
        'city.department.region.name',
        'city.department.region.country.slug',
        'city.department.region.country.name',
        'website',
        'status',
    ];

    public const ADMIN_FIELDS = [
        'company.id',
        'company.slug',
        'company.name',
        'company.legalName',
        'legalName',
        'siret',
        'phone',
        'email',
    ];
}
