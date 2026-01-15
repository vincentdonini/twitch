<?php

namespace App\Application\Serializer\JsonApi;

final class JsonApiQuery
{
    public function __construct(
        public readonly array      $filters = [],
        public readonly ?string    $search = null,
        public readonly Pagination $pagination = new Pagination(),
        public readonly Sorting    $sorting = new Sorting(),
        public readonly array      $includes = []
    ) {
    }
}
