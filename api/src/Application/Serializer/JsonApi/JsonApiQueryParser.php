<?php

namespace App\Application\Serializer\JsonApi;

use Symfony\Component\HttpFoundation\Request;

final class JsonApiQueryParser
{
    public function parse(Request $request): JsonApiQuery
    {
        $queryParams = $request->query->all();

        $pagination = $this->parsePagination($queryParams);
        $sorting    = $this->parseSorting($queryParams);
        [$filters, $search] = $this->parseFilters($queryParams);
        $includes = $this->parseIncludes($queryParams);

        return new JsonApiQuery(
            filters   : $filters,
            search    : $search,
            pagination: $pagination,
            sorting   : $sorting,
            includes  : $includes
        );
    }

    private function parsePagination(array $queryParams): Pagination
    {
        $page   = $queryParams['page'] ?? [];
        $offset = max(0, (int)($page['offset'] ?? 0));
        $limit  = (int)($page['limit'] ?? 20);

        if ($limit === -1) {
            $limit = PHP_INT_MAX;
        }

        return new Pagination(offset: $offset, limit: $limit);
    }

    private function parseSorting(array $queryParams): Sorting
    {
        $sortParam = $queryParams['sort'] ?? 'id';
        return new Sorting(
            field: ltrim($sortParam, '-'),
            order: str_starts_with($sortParam, '-') ? 'desc' : 'asc'
        );
    }

    private function parseFilters(array $queryParams): array
    {
        $allFilters = $queryParams['filter'] ?? [];
        $search     = $allFilters['search'] ?? null;
        unset($allFilters['search']);

        return [$allFilters, $search];
    }

    private function parseIncludes(array $queryParams): array
    {
        if (!isset($queryParams['include'])) {
            return [];
        }
        return array_map('trim', explode(',', $queryParams['include']));
    }
}
