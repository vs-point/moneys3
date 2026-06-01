<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Result;

/**
 * An immutable, typed list of items returned from a collection query.
 *
 * @template T
 * @implements \IteratorAggregate<int, T>
 */
final readonly class Collection implements \IteratorAggregate, \Countable
{
    /**
     * @param list<T> $items
     */
    public function __construct(
        public array $items,
    ) {
    }

    /**
     * @return T|null
     */
    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function count(): int
    {
        return \count($this->items);
    }

    /**
     * @return \ArrayIterator<int, T>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }
}
