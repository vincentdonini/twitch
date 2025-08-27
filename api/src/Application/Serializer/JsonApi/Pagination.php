<?php

namespace App\Application\Serializer\JsonApi;

final class Pagination
{
    public function __construct(
        public readonly int $offset = 0,
        public readonly int $limit = 20
    ) {
    }
}
