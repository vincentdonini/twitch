<?php

namespace App\Application\Serializer\JsonApi;

final class Sorting
{
    public function __construct(
        public readonly string $field = 'id',
        public readonly string $order = 'asc' // 'asc' | 'desc'
    )
    {
    }
}

