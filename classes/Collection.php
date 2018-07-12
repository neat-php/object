<?php

namespace Neat\Object;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use TypeError;

class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var string
     */
    protected $type;

    /**
     * Collection constructor
     *
     * @param array  $items
     * @param string $type
     */
    public function __construct(array $items, string $type = null)
    {
        $this->items = $items;
        if ($type) {
            $this->type = $type;
        } elseif (($item = reset($items)) && is_object($item)) {
            $this->type = get_class($item);
        }
    }

    /**
     * Validate item
     *
     * @param mixed  $item
     * @param string $method
     * @throws TypeError
     */
    protected function validate($item, $method)
    {
        if ($this->type && !$item instanceof $this->type) {
            $class = get_class($item);
            throw new TypeError("Argument 1 passed to $method must be of the type {$this->type}, $class given");
        }
    }

    /**
     * Get iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
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
        return isset($this->items[$offset]);
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
        return $this->items[$offset];
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
        $this->validate($value, __METHOD__);
        $this->items[$offset] = $value;
    }

    /**
     * Unset item at offset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * Count items
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Get first item
     *
     * @return mixed|false
     */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * Push item onto collection
     *
     * @param mixed $item
     * @return $this
     */
    public function push($item)
    {
        $this->validate($item, __METHOD__);
        $this->items[] = $item;

        return $this;
    }

    /**
     * Get filtered items as new collection
     *
     * The callback should accept an `$item` parameter
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback)
    {
        return new static(array_filter($this->items, $callback));
    }

    /**
     * Get mapped item results as array (or new collection when a class is specified)
     *
     * The callback should accept an `$item` parameter
     *
     * @param callable $callback
     * @param string   $class (optional)
     * @return array
     */
    public function map(callable $callback, $class = null)
    {
        if ($class) {
            return new $class($this->map($callback));
        }

        return array_map($callback, $this->items);
    }

    /**
     * Get given column for every item as array (or new collection when a class is specified)
     *
     * @param string $column
     * @param string $class (optional)
     * @return array
     */
    public function column($column, $class = null)
    {
        if ($class) {
            return new $class($this->column($column));
        }

        return array_column($this->items, $column);
    }

    /**
     * Get items for JSON serialization
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->items;
    }
}
