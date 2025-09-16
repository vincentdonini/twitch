<?php

namespace App\Infrastructure\ApiPlatform\Pagination;

use ApiPlatform\Doctrine\Orm\Paginator;
use Closure;
use IteratorAggregate;
use Traversable;
use Countable;

final class EnrichedPaginator implements IteratorAggregate, Countable
{
    public function __construct(
        private readonly Paginator $paginator,
        private readonly Closure $enricher
    ) {}

    public function getIterator(): Traversable
    {
        foreach ($this->paginator as $item) {
            yield ($this->enricher)($item);
        }
    }

    public function count(): int
    {
        return count($this->paginator);
    }

    public function getTotalItems(): int
    {
        return $this->paginator->getTotalItems();
    }

    public function getCurrentPage(): float
    {
        return $this->paginator->getCurrentPage();
    }

    public function getItemsPerPage(): float
    {
        return $this->paginator->getItemsPerPage();
    }

    public function getLastPage(): float
    {
        return $this->paginator->getLastPage();
    }
}
