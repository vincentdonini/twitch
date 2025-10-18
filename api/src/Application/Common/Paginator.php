<?php

namespace App\Application\Common;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class Paginator
{
    public function paginate(array $items, int $page, int $itemsPerPage): array
    {
        $paginator = new Pagerfanta(new ArrayAdapter($items));
        $paginator->setCurrentPage($page)
            ->setMaxPerPage($itemsPerPage);

        return $paginator->getCurrentPageResults();
    }

    public function getPaginationInfo(array $items, int $page, int $itemsPerPage): array
    {
        $totalItems = count($items);
        $totalPages = (int) ceil($totalItems / max(1, $itemsPerPage));

        return [
            'totalItems'   => $totalItems,
            'itemsPerPage' => $itemsPerPage,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
        ];
    }
}
