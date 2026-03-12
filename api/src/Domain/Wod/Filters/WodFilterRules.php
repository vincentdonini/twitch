<?php

namespace App\Domain\Wod\Filters;

use App\Domain\Common\Filter\Operator;

final class WodFilterRules
{
    public const ALLOWED_OPERATORS = [
        'name'                       => [Operator::EQ, Operator::LIKE],
        'teamSize'                   => [Operator::EQ, Operator::LT, Operator::LTE, Operator::GT, Operator::GTE],
        'wodType.id'                 => [Operator::EQ, Operator::NEQ, Operator::IN, Operator::NOT_IN],
        'wodType.slug'               => [Operator::EQ],
        'wodCategory.id'             => [Operator::EQ, Operator::NEQ, Operator::IN, Operator::NOT_IN],
        'wodCategory.slug'           => [Operator::EQ],
        'wodVariants.wodDivision.id'   => [Operator::EQ, Operator::NEQ, Operator::IN, Operator::NOT_IN],
        'wodVariants.wodDivision.slug' => [Operator::EQ],
        'wodVariants.gender'           => [Operator::EQ],
        'wodVariants.rounds'           => [Operator::EQ, Operator::LT, Operator::LTE, Operator::GT, Operator::GTE],
        'wodVariants.timeCap'          => [Operator::EQ, Operator::LT, Operator::LTE, Operator::GT, Operator::GTE],
    ];

    public const PUBLIC_FIELDS = [
        'name',
        'teamSize',
        'wodType.id',
        'wodType.slug',
        'wodCategory.id',
        'wodCategory.slug',
        'wodVariants.wodDivision.id',
        'wodVariants.wodDivision.slug',
        'wodVariants.gender',
        'wodVariants.rounds',
        'wodVariants.timeCap',
    ];
}
