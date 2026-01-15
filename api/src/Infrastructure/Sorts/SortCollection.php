<?php

namespace App\Infrastructure\Sorts;


use Countable;
use IteratorAggregate;
use Traversable;

final class SortCollection implements IteratorAggregate, Countable
{
    public function __construct(
        private array $sorts = []
    ) {
    }

    public function all(): array
    {
        return $this->sorts;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->sorts);
    }

    public function count(): int
    {
        return count($this->sorts);
    }

    public function isEmpty(): bool
    {
        return empty($this->sorts);
    }
}