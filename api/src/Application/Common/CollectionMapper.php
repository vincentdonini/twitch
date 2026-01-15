<?php

namespace App\Application\Common;


final class CollectionMapper
{
    public static function mapAndFilter(array $items, callable $callback): array
    {
        return array_filter(
            array_map($callback, $items),
            fn($value) => $value !== null
        );
    }
}