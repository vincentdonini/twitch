<?php

namespace App\Domain\Wod\Filters;

use App\Domain\Common\Filter\Operator;

final class WodFilterRules
{
    public const ALLOWED_OPERATORS = [
        'name'                                   => [
            Operator::EQ,
            Operator::LIKE,
        ],
        'teamSize'                               => [
            Operator::EQ,
            Operator::LT,
            Operator::LTE,
            Operator::GT,
            Operator::GTE,
        ],
        'wodType.id'                             => [
            Operator::EQ,
        ],
        'wodType.slug'                           => [
            Operator::EQ,
        ],
        'wodCategory.id'                         => [
            Operator::EQ,
        ],
        'wodCategory.slug'                       => [
            Operator::EQ,
        ],
        'wodVersions.wodDivision.id'             => [
            Operator::EQ,
        ],
        'wodVersions.wodDivision.slug'           => [
            Operator::EQ,
        ],
        'wodVersions.gender'                     => [
            Operator::EQ,
        ],
        'wodVersions.wodVariants.rounds'  => [
            Operator::EQ,
            Operator::LT,
            Operator::LTE,
            Operator::GT,
            Operator::GTE,
        ],
        'wodVersions.wodVariants.timeCap' => [
            Operator::EQ,
            Operator::LT,
            Operator::LTE,
            Operator::GT,
            Operator::GTE,
        ],
    ];
}
