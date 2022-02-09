<?php

namespace Neat\Object;

use ArrayIterator;

/**
 * @template T
 */
trait Collectible
{
    /**
     * Get iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items());
    }

    /**
     * Item at offset exists?
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->items()[$offset]);
    }

    /**
     * Get item at offset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return T|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->items()[$offset];
    }

    /**
     * Set item at offset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset
     * @param T $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $items          = &$this->items();
        $items[$offset] = $value;
    }

    /**
     * Unset item at offset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $items = &$this->items();
        unset($items[$offset]);
    }

    /**
     * Get all items
     *
     * @return array<T>
     */
    public function all(): array
    {
        return $this->items();
    }

    /**
     * Count items
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    public function count(): int
    {
        return count($this->items());
    }

    /**
     * Get first item
     *
     * @return T|null
     */
    public function first()
    {
        $items = $this->items();
        if ($this->count() === 0) {
            return null;
        }

        return reset($items);
    }

    /**
     * Get last item
     *
     * @return T|null
     */
    public function last()
    {
        $items = $this->items();
        if ($this->count() === 0) {
            return null;
        }

        return end($items);
    }

    /**
     * Push item onto collection
     *
     * @param T $item
     * @return $this
     */
    public function push($item): self
    {
        $items   = &$this->items();
        $items[] = $item;

        return $this;
    }

    /**
     * Get filtered items as new collection
     *
     * The callback should accept an `$item` parameter
     *
     * @param callable|null $callback
     * @psalm-param callable(T):bool|null $callback
     * @return static
     */
    public function filter(callable $callback = null): self
    {
        if (!$callback) {
            $callback = [$this, 'falsyFilter'];
        }
        $new   = clone $this;
        $items = &$new->items();
        foreach ($items as $key => $item) {
            if (!$callback($item)) {
                unset($items[$key]);
            }
        }

        return $new;
    }

    /**
     * @param T $item
     * @return bool
     */
    public function falsyFilter($item): bool
    {
        return !!$item;
    }

    /**
     * Get mapped item results as new collection
     *
     * The callback should accept an `$item` parameter
     *
     * @psalm-template TReturn
     * @param callable $callback
     * @psalm-param callable(T):TReturn $callback
     * @return Collection<TReturn>
     */
    public function map(callable $callback): Collection
    {
        return new Collection(array_map($callback, $this->items()));
    }

    /**
     * Get given column for every item as new collection
     *
     * @param string $column
     * @return Collection
     */
    public function column(string $column): Collection
    {
        return new Collection(array_column($this->items(), $column));
    }

    /**
     * @param null|callable(T):int $callback
     * @return static<T>
     */
    public function sort(callable $callback = null): self
    {
        $new   = clone $this;
        $items = &$new->items();
        if ($callback) {
            usort($items, $callback);
        } else {
            sort($items);
        }

        return $new;
    }

    public function jsonSerialize(): array
    {
        return $this->items();
    }

    /**
     * @return array<T>
     */
    abstract protected function &items(): array;
}
