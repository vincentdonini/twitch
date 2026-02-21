<?php

namespace App\Infrastructure\Paginator;


use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use DateTimeInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class ResponsePaginator
{
    public static function buildPaginationHeaders(
        Paginator|LightPaginator|array $paginator,
        PaginatorValues                $paginatorValues
    ): array {
        if ($paginator instanceof Paginator || $paginator instanceof LightPaginator) {
            $totalCount = $paginator->count();
        } else {
            $totalCount = count($paginator);
        }

        $limit = $paginatorValues->getLimit();

        if ($limit === -1) {
            $headers = [
                'Pagination-Page'  => 1,
                'Pagination-Count' => $totalCount,
                'Element-Count'    => $totalCount,
                'Pagination-Limit' => $totalCount,
            ];
        } else {
            $headers = [
                'Pagination-Page'  => $paginatorValues->getPage(),
                'Pagination-Count' => (int) ceil($totalCount / $limit),
                'Element-Count'    => $totalCount,
                'Pagination-Limit' => $limit,
            ];
        }

        if ($paginator instanceof LightPaginator) {
            $lastModified = $paginator->getLastModified();
            if ($lastModified) {
                $headers['Last-Modified'] = $lastModified->format(DateTimeInterface::RFC7231);
            }
        }

        return $headers;
    }

    public static function buildPaginationResponseBlock(
        ?Paginator      $paginator,
        PaginatorValues $paginatorValues,
                        $values
    ): array {
        if ($paginator) {
            $total = $paginator->count();
            $pages = ceil(
                count($paginator) / $paginatorValues->getLimit()
            );
        } elseif (is_array($values)) {
            $total = $values ? count($values) : 0;
            $pages = $values ? ceil(
                count($values) / $paginatorValues->getLimit()
            ) : 0;
        } else {
            return [];
        }
        return [
            'total' => $total,
            'page'  => $paginatorValues->getPage(),
            'pages' => $pages,
            'limit' => $paginatorValues->getLimit(),
        ];
    }
}
