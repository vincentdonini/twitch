<?php

namespace App\Infrastructure\Sorts;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

final class RequestSort
{
    public static function extractValues(
        Request $request,
        array   $fieldMap,
        ?string $defaultSort = null
    ): SortCollection {
        $sortParam = $request->query->get('sort', $defaultSort);

        try {
            return SortParser::parse($sortParam, $fieldMap);
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException(sprintf("Invalid sort parameter: %s", $e->getMessage()));
        }
    }
}
