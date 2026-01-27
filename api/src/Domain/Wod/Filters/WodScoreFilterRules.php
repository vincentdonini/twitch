<?php

namespace App\Domain\Wod\Filters;

use App\Domain\Common\Filter\Operator;

final class WodScoreFilterRules
{
    public const ALLOWED_OPERATORS = [
        'user.id'                     => [Operator::EQ],
        'user.firstName'              => [Operator::EQ, Operator::LIKE],
        'user.lastName'               => [Operator::EQ, Operator::LIKE],
        'wod.id'                      => [Operator::EQ],
        'wod.name'                    => [Operator::EQ, Operator::LIKE],
        'wodVersion.wodDivision.id'   => [Operator::EQ],
        'wodVersion.wodDivision.slug' => [Operator::EQ],
        'wodVariant.gender'           => [Operator::EQ],
    ];

    public const PUBLIC_FIELDS = [
        'user.id',
        'user.firstName',
        'user.lastName',
        'wod.id',
        'wod.name',
        'wodVersion.wodDivision.id',
        'wodVersion.wodDivision.slug',
        'wodVariant.gender',
    ];
}
