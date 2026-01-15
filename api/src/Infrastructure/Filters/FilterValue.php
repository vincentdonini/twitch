<?php

namespace App\Infrastructure\Filters;

use App\Domain\Common\Filter\Operator;

final readonly class FilterValue
{
    public function __construct(
        public string   $field,
        public Operator $operator,
        public mixed    $value,
    ) {
    }
}