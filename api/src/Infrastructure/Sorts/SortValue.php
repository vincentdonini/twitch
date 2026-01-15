<?php

namespace App\Infrastructure\Sorts;

final readonly class SortValue
{
    public function __construct(
        public string        $field,
        public SortDirection $direction
    ) {
    }
}