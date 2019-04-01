<?php

namespace Neat\Object;

use ArrayIterator;

trait Collectible
{
    /**
     * Get iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return ArrayIterator
     */
    public function getIterator()
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
    public function offsetExists($offset)
    {
        return isset($this->items()[$offset]);
    }

    /**
     * Get item at offset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->items()[$offset];
    }

    /**
     * Set item at offset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $items          = &$this->items();
        $items[$offset] = $value;
    }

    /**
     * Unset item at offset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $items = &$this->items();
        unset($items[$offset]);
        $this->items($items);
    }

    /**
     * Get all items
     *
     * @return array
     */
    public function all()
    {
        return $this->items();
    }

    /**
     * Count items
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    public function count()
    {
        return count($this->items());
    }

    /**
     * Get first item
     *
     * @return mixed|false
     */
    public function first()
    {
        $items = $this->items();

        return reset($items);
    }

    /**
     * Get last item
     *
     * @return mixed|false
     */
    public function last()
    {
        $items = $this->items();

        return end($items);
    }

    /**
     * Push item onto collection
     *
     * @param mixed $item
     * @return $this
     */
    public function push($item)
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
     * @param callable $callback (optional)
     * @return static
     */
    public function filter(callable $callback = null)
    {
        if (!$callback) {
            $callback = function ($item) {
                return !!$item;
            };
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
     * Get mapped item results as new collection
     *
     * The callback should accept an `$item` parameter
     *
     * @param callable $callback
     * @return Collection|array
     */
    public function map(callable $callback)
    {
        return new Collection(array_map($callback, $this->items()));
    }

    /**
     * Get given column for every item as new collection
     *
     * @param string $column
     * @return Collection|array
     */
    public function column($column)
    {
        return new Collection(array_column($this->items(), $column));
    }

    /**
     * Get items for JSON serialization
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->items();
    }

    protected abstract function &items(): array;
}
