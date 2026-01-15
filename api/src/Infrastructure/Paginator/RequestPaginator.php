<?php

namespace App\Infrastructure\Paginator;

use Symfony\Component\HttpFoundation\Request;

final class RequestPaginator
{
    public const DEFAULT_LIMIT   = 15;
    public const MAX_LIMIT_VALUE = 5000;

    public static function extractValues(Request $request): PaginatorValues
    {
        $page  = $request->query->get('page', 1);
        $limit = $request->query->get('limit', self::DEFAULT_LIMIT);

        if (!is_numeric($page) || !is_numeric($limit)) {
            throw new InvalidPaginationArgumentException(sprintf("Pagination values provided must be an integer"));
        }

        if ($page < 1) {
            throw new InvalidPaginationArgumentException(sprintf("Pagination page provided must be greater than 0"));
        }

        if ($limit < 1 || $limit > self::MAX_LIMIT_VALUE) {
            throw new InvalidPaginationArgumentException(
                sprintf(
                    "Pagination limit provided must be between 1 and %d",
                    self::MAX_LIMIT_VALUE
                )
            );
        }

        return new PaginatorValues($page, $limit);
    }
}
