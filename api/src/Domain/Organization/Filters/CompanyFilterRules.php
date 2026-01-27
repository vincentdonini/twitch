<?php

namespace App\Domain\Organization\Filters;

use App\Domain\Common\Filter\Operator;

final class CompanyFilterRules
{
    public const ALLOWED_OPERATORS = [
        'slug'                                => [Operator::EQ],
        'name'                                => [Operator::EQ, Operator::LIKE],
        'legalName'                           => [Operator::EQ, Operator::LIKE],
        'siren'                               => [Operator::EQ],
        'vatNumber'                           => [Operator::EQ],
        'activityCode'                        => [Operator::EQ],
        'legalForm'                           => [Operator::EQ],
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
        'status',
    ];

    public const ADMIN_FIELDS = [
        'legalName',
        'siren',
        'vatNumber',
        'activityCode',
        'legalForm',
        'phone',
        'email',
    ];
}
