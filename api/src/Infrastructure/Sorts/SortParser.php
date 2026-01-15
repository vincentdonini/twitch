<?php

namespace App\Infrastructure\Sorts;

use InvalidArgumentException;

final class SortParser
{
    public static function parse(?string $sort, array $fieldMap): SortCollection
    {
        if (!$sort) {
            return new SortCollection();
        }

        $result = [];

        foreach (explode(',', $sort) as $part) {
            $part = trim($part);

            $direction = str_starts_with($part, '-')
                ? SortDirection::DESC
                : SortDirection::ASC;

            $apiField = ltrim($part, '-');

            if (!array_key_exists($apiField, $fieldMap)) {
                throw new InvalidArgumentException(sprintf('Sorting by "%s" is not allowed.', $apiField));
            }

            $result[] = new SortValue(
                $fieldMap[$apiField],
                $direction
            );
        }

        return new SortCollection($result);
    }
}
