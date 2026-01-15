<?php

namespace App\Infrastructure\Filters;


use Countable;
use IteratorAggregate;
use Traversable;

final readonly class FilterCollection implements IteratorAggregate, Countable
{
    public function __construct(
        private array $filters = []
    ) {
    }

    public function all(): array
    {
        return $this->filters;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->filters);
    }

    public function count(): int
    {
        return count($this->filters);
    }

    public function isEmpty(): bool
    {
        return empty($this->filters);
    }
}