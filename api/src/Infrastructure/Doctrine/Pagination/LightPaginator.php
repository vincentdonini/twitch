<?php

namespace App\Infrastructure\Doctrine\Pagination;

use ArrayIterator;
use Countable;
use DateTimeImmutable;
use IteratorAggregate;

class LightPaginator implements Countable, IteratorAggregate
{
    public function __construct(
        private readonly array              $items,
        private readonly int                $count,
        private readonly int                $page = 1,
        private readonly int                $limit = 15,
        private readonly ?DateTimeImmutable $lastModified = null
    ) {
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getPages(): int
    {
        return (int)ceil($this->count / $this->limit);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return $this->count;
    }

    public function getLastModified(): ?DateTimeImmutable
    {
        return $this->lastModified;
    }
}
