<?php

namespace App\Infrastructure\Paginator;

use Symfony\Component\HttpFoundation\Request;

final class RequestPaginator
{
    public const int DEFAULT_LIMIT    = 15;
    public const int MAX_LIMIT_VALUE  = 5000;
    public const int NO_PAGINATION    = -1;

    public static function extractValues(Request $request): PaginatorValues
    {
        $page  = $request->query->get('page', 1);
        $limit = $request->query->get('limit', self::DEFAULT_LIMIT);

        if (!is_numeric($page) || !is_numeric($limit)) {
            throw new InvalidPaginationArgumentException(
                "Pagination values provided must be integers"
            );
        }

        $page  = (int) $page;
        $limit = (int) $limit;

        if ($limit === self::NO_PAGINATION) {
            return new PaginatorValues(1, self::NO_PAGINATION);
        }

        if ($page < 1) {
            throw new InvalidPaginationArgumentException(
                "Pagination page provided must be greater than 0"
            );
        }

        if ($limit < 1 || $limit > self::MAX_LIMIT_VALUE) {
            throw new InvalidPaginationArgumentException(
                sprintf(
                    "Pagination limit provided must be between 1 and %d or -1 to disable pagination",
                    self::MAX_LIMIT_VALUE
                )
            );
        }

        return new PaginatorValues($page, $limit);
    }
}
